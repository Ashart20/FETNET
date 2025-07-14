<?php

namespace App\Livewire\Prodi;

use App\Exports\TeacherTemplateExport;
use App\Imports\TeachersImport;
use App\Models\Prodi;
use App\Models\Teacher;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Mary\Traits\Toast;

class ManageTeachers extends Component
{
    use Toast, WithFileUploads, WithPagination;

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

    // Properti untuk fitur Dosen Tamu
    public string $teacherSearch = '';
    public \Illuminate\Support\Collection $teacherSearchResults;

    public function mount()
    {
        $this->teacherSearchResults = collect();
    }

    public function rules()
    {
        return [
            'nama_dosen' => 'required|string|max:255',
            'kode_dosen' => ['required', 'string', 'max:20', Rule::unique('teachers')->ignore($this->teacherId)],
            'title_depan' => 'nullable|string|max:50',
            'title_belakang' => 'nullable|string|max:100',
            'kode_univ' => ['nullable', 'string', 'max:255', Rule::unique('teachers')->ignore($this->teacherId)],
            'employee_id' => ['nullable', 'string', 'max:255', Rule::unique('teachers')->ignore($this->teacherId)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('teachers')->ignore($this->teacherId)],
            'nomor_hp' => 'nullable|string|max:20',
        ];
    }

    protected $messages = [
        'required' => ':attribute wajib diisi.',
        'unique' => ':attribute ini sudah terdaftar.',
        'email' => 'Format :attribute tidak valid.',
    ];

    public function headers(): array
    {
        return [
            ['key' => 'kode_dosen', 'label' => 'Kode Dosen'],
            ['key' => 'full_name', 'label' => 'Nama Lengkap Dosen', 'sortable' => true],
            ['key' => 'kode_univ', 'label' => 'NIDN'],
            ['key' => 'actions', 'label' => '', 'class' => 'w-1'],
        ];
    }

    public function render()
    {
        $currentProdi = auth()->user()->prodi;

        //  Ambil semua dosen dari dalam cluster yang sama
        $clusterTeacherIds = collect();
        if ($currentProdi && $currentProdi->cluster_id) {
            $prodiIdsInCluster = Prodi::where('cluster_id', $currentProdi->cluster_id)->pluck('id');
            $clusterTeacherIds = DB::table('prodi_teacher')
                ->whereIn('prodi_id', $prodiIdsInCluster)
                ->pluck('teacher_id');
        }

        // Ambil semua dosen yang terhubung manual ke prodi ini
        $linkedTeacherIds = $currentProdi ? $currentProdi->teachers()->pluck('teachers.id') : collect();

        //  Gabungkan kedua daftar ID dan hilangkan duplikat
        $allTeacherIds = $clusterTeacherIds->merge($linkedTeacherIds)->unique();

        // Kueri dasar: Ambil dosen berdasarkan daftar ID gabungan
        $teachersQuery = Teacher::whereIn('id', $allTeacherIds);


        // Tambahkan relasi jika dalam mode laporan
        if ($this->viewMode === 'report') {
            $teachersQuery->with(['activities.subject', 'activities.prodi']);
        }

        $teachers = $teachersQuery->orderBy('nama_dosen')->paginate(10);

        return view('livewire.prodi.manage-teachers', [
            'teachers' => $teachers,
            'headers' => $this->headers(),
        ])->layout('layouts.app');
    }

    public function updatedTeacherSearch(string $value): void
    {
        if (strlen($value) < 3) {
            $this->teacherSearchResults = collect();
            return;
        }

        $existingTeacherIds = auth()->user()->prodi->teachers()->pluck('id');
        $this->teacherSearchResults = Teacher::where(function ($query) use ($value) {
            $query->where('nama_dosen', 'like', "%{$value}%")
                ->orWhere('kode_dosen', 'like', "%{$value}%");
        })
            ->whereNotIn('id', $existingTeacherIds)
            ->limit(7)
            ->get();
    }

    public function linkTeacher(int $teacherId): void
    {
        auth()->user()->prodi->teachers()->syncWithoutDetaching([$teacherId]);
        $this->teacherSearch = '';
        $this->teacherSearchResults = collect();
        $this->success('Dosen berhasil dihubungkan ke prodi Anda.');
    }

    public function store()
    {
        $validatedData = $this->validate();
        $teacher = Teacher::updateOrCreate(['id' => $this->teacherId], $validatedData);
        $teacher->prodis()->syncWithoutDetaching([auth()->user()->prodi_id]);
        $this->toast(type: 'success', title: $this->teacherId ? 'Data Dosen Diperbarui.' : 'Data Dosen Ditambahkan.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $teacher = Teacher::whereHas('prodis', fn ($q) => $q->where('prodis.id', auth()->user()->prodi_id))->findOrFail($id);
        $this->teacherId = $id;
        $this->fill($teacher->toArray());
        $this->teacherModal = true;
    }

    public function unlinkTeacher($id)
    {
        try {
            $teacher = Teacher::findOrFail($id);
            $teacher->prodis()->detach(auth()->user()->prodi_id);
            if ($teacher->prodis()->count() === 0) {
                $teacher->delete();
            }
            $this->toast(type: 'warning', title: 'Hubungan Dosen Telah Diputus dari Prodi Anda.');
        } catch (\Exception $e) {
            $this->toast(type: 'error', title: 'Gagal memutus hubungan.', description: 'Mungkin dosen terhubung dengan data aktivitas.');
        }
    }

    public function updatedFile()
    {
        $this->validate(['file' => 'required|mimes:xlsx|max:10240']);
        try {
            Excel::import(new TeachersImport(auth()->user()->prodi_id), $this->file);
            $this->success('Semua data dosen berhasil diimpor.');
            $this->reset('file');
        } catch (ValidationException $e) {
            $errorMessages = [];
            foreach ($e->failures() as $failure) {
                $errorMessages[] = "Baris ke-{$failure->row()}: ".implode(', ', $failure->errors());
            }
            $this->error('Impor Gagal: Ditemukan kesalahan validasi', implode('<br>', $errorMessages), timeout: 15000);
        } catch (\Exception $e) {
            $this->error('Impor Gagal.', 'Pastikan format file Excel Anda sudah benar. Detail: '.$e->getMessage(), timeout: 10000);
        }
    }

    public function downloadTemplate()
    {
        $filename = 'templates/template_data_dosen.xlsx';
        return Storage::disk('local')->download($filename, 'template_dosen.xlsx');
    }

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

        $this->reset([
            'teacherId',
            'nama_dosen',
            'kode_dosen',
            'title_depan',
            'title_belakang',
            'kode_univ',
            'employee_id',
            'email',
            'nomor_hp',
            'teacherSearch',
            'file'
        ]);

        $this->teacherSearchResults = collect();

        $this->resetErrorBag();
    }
}
