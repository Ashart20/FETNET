<?php

namespace App\Livewire\Prodi;

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

    use WithPagination, Toast, WithFileUploads;

    // Properti untuk mengontrol tampilan
    public string $viewMode = 'manage';

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
     * Pesan validasi kustom.
     */
    protected $messages = [
        'required' => ':attribute wajib diisi.',
        'unique'   => ':attribute ini sudah terdaftar.',
        'email'    => 'Format :attribute tidak valid.',
    ];

    /**
     * Mendefinisikan header untuk tabel di mode manajemen.
     */
    public function headers(): array
    {
        return [
            ['key' => 'kode_dosen', 'label' => 'Kode Dosen'],
            ['key' => 'nama_dosen', 'label' => 'Nama Lengkap Dosen', 'sortable' => true],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'nomor_hp', 'label' => 'No. HP'],
            ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-1'],
        ];
    }

    /**
     * Merender komponen, mengambil data sesuai mode yang aktif.
     */
    public function render()
    {
        $currentProdi = auth()->user()->prodi;

        $teachersQuery = Teacher::query()
            ->with(['activities.subject', 'activities.prodi']);

        if ($currentProdi) {
            // Logika untuk cluster prodi
            if ($currentProdi->cluster_id) {
                $prodiIdsInCluster = Prodi::where('cluster_id', $currentProdi->cluster_id)->pluck('id');
                $teachersQuery->whereHas('prodis', function ($query) use ($prodiIdsInCluster) {
                    $query->whereIn('prodis.id', $prodiIdsInCluster);
                })->distinct();
            } else {
                $teachersQuery->whereHas('prodis', function ($query) use ($currentProdi) {
                    $query->where('prodis.id', $currentProdi->id);
                });
            }
        } else {
            $teachersQuery->whereRaw('1 = 0');
        }

        $teachers = $teachersQuery->orderBy('nama_dosen', 'asc')->paginate(10);

        return view('livewire.prodi.manage-teachers', [
            'teachers' => $teachers,
            'headers' => $this->headers()
        ])->layout('layouts.app');
    }

    /**
     * Mengubah mode tampilan antara 'manage' dan 'report'.
     */
    public function setViewMode(string $mode)
    {
        $this->viewMode = $mode;
        $this->resetPage(); // Reset paginasi setiap kali beralih mode
    }

    /**
     * Menyimpan data dosen (membuat baru atau memperbarui).
     */
    public function store()
    {
        $validatedData = $this->validate();
        $prodiId = auth()->user()->prodi_id;

        $teacher = Teacher::updateOrCreate(['id' => $this->teacherId], $validatedData);
        $teacher->prodis()->syncWithoutDetaching([$prodiId]);

        $this->toast(type: 'success', title: $this->teacherId ? 'Data Dosen Berhasil Diperbarui.' : 'Data Dosen Berhasil Ditambahkan.');
        $this->closeModal();
    }

    /**
     * Menyiapkan form modal untuk mengedit data.
     */
    public function edit($id)
    {
        $teacher = Teacher::whereHas('prodis', fn($q) => $q->where('prodis.id', auth()->user()->prodi_id))->findOrFail($id);

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
     * Menghapus relasi dosen dari prodi dan menghapus data dosen jika tidak terikat prodi lain.
     */
    public function delete($id)
    {
        try {
            $teacher = Teacher::whereHas('prodis', fn($q) => $q->where('prodis.id', auth()->user()->prodi_id))->findOrFail($id);
            $teacher->prodis()->detach(auth()->user()->prodi_id);

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
        $this->validate(['file' => 'required|mimes:xlsx|max:10240']);

        try {
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
            ['nama_dosen', 'kode_dosen', 'title_depan', 'title_belakang', 'kode_univ', 'employee_id', 'email', 'nomor_hp'],
            ['Budi Setiawan', 'BDS', 'Dr.', 'M.Kom.', '0123456789', 'NIP001', 'budi.s@example.ac.id', '081234567890'],
            ['Siti Aminah', 'STA', '', 'S.Pd., M.Pd.', '0987654321', 'NIP002', 'siti.a@example.ac.id', '089876543210'],
        ];

        Excel::store(new TeacherTemplateExport($data), $filename, $disk);
        return Storage::disk($disk)->download($filename);
    }

    /**
     * Fungsi-fungsi helper untuk UI.
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
        $this->resetExcept('viewMode'); // Jangan reset viewMode
        $this->resetErrorBag();
    }
}
