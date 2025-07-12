<?php

namespace App\Livewire\Prodi;

// Namespace dan Class yang diperlukan
use App\Models\Prodi;
use App\Models\Teacher;
use App\Exports\TeacherTemplateExport;
use App\Imports\TeachersImport;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Support\Facades\Storage;
use Mary\Traits\Toast;

class ManageTeachers extends Component
{
    // Menggunakan traits untuk fungsionalitas tambahan
    use WithPagination, Toast, WithFileUploads;

    // Properti untuk data di form modal
    public ?int $teacherId = null;
    public string $nama_dosen = '';
    public string $kode_dosen = '';
    public string $title_depan = '';
    public string $title_belakang = '';
    public string $kode_univ = '';
    public string $employee_id = '';
    public string $email = '';
    public string $nomor_hp = '';

    // Properti untuk kontrol UI
    public bool $teacherModal = false;

    // Properti untuk file upload
    public $file;

    /**
     * Aturan validasi dinamis untuk form.
     */
    public function rules()
    {
        return [
            'nama_dosen' => 'required|string|max:255',
            'kode_dosen' => [
                'required',
                'string',
                'max:20',
                // Pastikan kode dosen unik di seluruh tabel, kecuali untuk ID saat ini
                Rule::unique('teachers')->ignore($this->teacherId),
            ],
            'title_depan' => 'nullable|string|max:50',
            'title_belakang' => 'nullable|string|max:100',
            'kode_univ' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('teachers')->ignore($this->teacherId),
            ],
            'employee_id' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('teachers')->ignore($this->teacherId),
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('teachers')->ignore($this->teacherId),
            ],
            'nomor_hp' => 'nullable|string|max:20',
        ];
    }

    /**
     * Pesan validasi kustom agar lebih ramah pengguna.
     */
    protected $messages = [
        'required' => ':attribute wajib diisi.',
        'unique'   => ':attribute ini sudah terdaftar.',
        'email'    => 'Format :attribute tidak valid.',
    ];

    /**
     * Mendefinisikan header untuk komponen tabel MaryUI.
     */
    public function headers(): array
    {
        return [
            ['key' => 'kode_dosen', 'label' => 'Kode Dosen'],
            ['key' => 'nama_dosen', 'label' => 'Nama Lengkap Dosen', 'sortable' => true],
            ['key' => 'kode_univ', 'label' => 'Kode Universitas'],
            ['key' => 'employee_id', 'label' => 'Employee Id'],
            ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-1'],
        ];
    }

    /**
     * Merender komponen, mengambil data dosen sesuai prodi pengguna.
     */
    public function render()
    {
        $currentProdi = auth()->user()->prodi;
        $teachersQuery = Teacher::query();

        if ($currentProdi) {
            // Jika prodi tergabung dalam cluster, tampilkan semua dosen dalam cluster tsb.
            if ($currentProdi->cluster_id) {
                $prodiIdsInCluster = Prodi::where('cluster_id', $currentProdi->cluster_id)->pluck('id');
                $teachersQuery->whereHas('prodis', function ($query) use ($prodiIdsInCluster) {
                    $query->whereIn('prodis.id', $prodiIdsInCluster);
                })->distinct();
            } else {
                // Jika tidak, tampilkan hanya dosen dari prodi tersebut.
                $teachersQuery->whereHas('prodis', function ($query) use ($currentProdi) {
                    $query->where('prodis.id', $currentProdi->id);
                });
            }
        } else {
            // Jika pengguna tidak memiliki prodi, jangan tampilkan data apa pun.
            $teachersQuery->whereRaw('1 = 0');
        }

        $teachers = $teachersQuery->orderBy('nama_dosen', 'asc')->paginate(10);

        return view('livewire.prodi.manage-teachers', [
            'teachers' => $teachers,
            'headers' => $this->headers()
        ])->layout('layouts.app');
    }

    /**
     * Menyimpan data dosen (baik membuat baru atau memperbarui).
     */
    public function store()
    {
        $validatedData = $this->validate();
        $prodiId = auth()->user()->prodi_id;

        // Buat atau update data di tabel 'teachers'
        $teacher = Teacher::updateOrCreate(['id' => $this->teacherId], $validatedData);

        // Sinkronkan dosen dengan prodi saat ini tanpa melepaskan dari prodi lain
        $teacher->prodis()->syncWithoutDetaching([$prodiId]);

        $this->toast(type: 'success', title: $this->teacherId ? 'Data Dosen Berhasil Diperbarui.' : 'Data Dosen Berhasil Ditambahkan.');
        $this->closeModal();
    }

    /**
     * Menyiapkan form modal untuk mengedit data dosen yang ada.
     */
    public function edit($id)
    {
        $prodiId = auth()->user()->prodi_id;
        $teacher = Teacher::whereHas('prodis', fn($q) => $q->where('prodis.id', $prodiId))->findOrFail($id);

        $this->teacherId = $id;
        $this->nama_dosen = $teacher->nama_dosen;
        $this->kode_dosen = $teacher->kode_dosen;
        $this->title_depan = $teacher->title_depan ?? '';
        $this->title_belakang = $teacher->title_belakang ?? '';
        $this->kode_univ = $teacher->kode_univ ?? '';
        $this->employee_id = $teacher->employee_id ?? '';
        $this->email = $teacher->email ?? '';
        $this->nomor_hp = $teacher->nomor_hp ?? '';

        $this->teacherModal = true;
    }

    /**
     * Menghapus relasi dosen dari prodi saat ini.
     * Jika dosen tidak lagi terikat pada prodi mana pun, data dosen akan dihapus permanen.
     */
    public function delete($id)
    {
        try {
            $prodiId = auth()->user()->prodi_id;
            $teacher = Teacher::whereHas('prodis', fn($q) => $q->where('prodis.id', $prodiId))->findOrFail($id);

            // Lepaskan dosen dari prodi saat ini
            $teacher->prodis()->detach($prodiId);

            // Jika dosen tidak lagi memiliki relasi dengan prodi lain, hapus datanya.
            if ($teacher->prodis()->count() === 0) {
                $teacher->delete();
            }
            $this->toast(type: 'warning', title: 'Data Dosen Berhasil Dihapus dari Prodi Anda.');
        } catch (\Exception $e) {
            $this->toast(type: 'error', title: 'Gagal menghapus dosen.', description: 'Mungkin terhubung dengan data lain.');
        }
    }

    /**
     * Menangani unggahan file Excel dan memulai proses impor.
     */
    public function updatedFile()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx|max:10240', // Maks 10MB
        ]);

        try {
            // Gunakan class import yang sudah direvisi
            Excel::import(new TeachersImport(auth()->user()->prodi_id), $this->file);

            $this->success('Semua data dosen berhasil diimpor.', position: 'toast-bottom');
            $this->reset('file');

        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris ke-{$failure->row()}: " . implode(', ', $failure->errors());
            }
            $this->error('Impor Gagal: Ditemukan kesalahan validasi', implode("<br>", $errorMessages), timeout: 15000);
        } catch (\Exception $e) {
            $this->error('Impor Gagal.', 'Pastikan format dan header file Excel Anda sudah benar. Detail: ' . $e->getMessage(), timeout: 10000);
        }
    }

    /**
     * Membuat dan menyediakan file template Excel untuk diunduh.
     */
    public function downloadTemplate()
    {
        $filename = 'templates/template_data_dosen.xlsx';
        $disk = 'local';

        $data = [
            // Header (ini akan digunakan oleh class Export)
            ['nama_dosen', 'kode_dosen', 'title_depan', 'title_belakang', 'kode_univ', 'employee_id', 'email', 'nomor_hp'],
            // Contoh data
            ['Budi Setiawan', 'BDS', 'Dr.', 'M.Kom.', 'E1234', 'NIP001', 'budi.s@upi.edu', '081234567890'],
            ['Siti Aminah', 'STA', '', 'S.Pd., M.Pd.', 'E2345', 'NIP002', 'siti.a@upi.edu', '089876543210'],
        ];

        Excel::store(new TeacherTemplateExport($data), $filename, $disk);

        return Storage::disk($disk)->download($filename);
    }

    /**
     * Fungsi helper untuk membuka modal dan mereset input fields.
     */
    public function create()
    {
        $this->resetInputFields();
        $this->teacherModal = true;
    }

    public function closeModal()
    {
        $this->teacherModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->reset();
        $this->resetErrorBag();
    }
}
