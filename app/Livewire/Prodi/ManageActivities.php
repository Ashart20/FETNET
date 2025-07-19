<?php

namespace App\Livewire\Prodi;

use App\Models\Activity;
use App\Models\ActivityTag;
use App\Models\Prodi;
use App\Models\StudentGroup;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class ManageActivities extends Component
{
    use WithPagination, Toast;

    // Koleksi data untuk pilihan di form
    public EloquentCollection $teachers;
    public EloquentCollection $subjects;
    public EloquentCollection $allStudentGroups;
    public EloquentCollection $activityTags;

    // Properti untuk form
    public ?int $activityId = null;
    public array $teacher_ids = [];
    public ?int $subject_id = null;
    public array $selectedStudentGroupIds = [];
    public ?int $activity_tag_id = null;
    public $practicum_sks = null;
    public ?string $name = null;

    // Properti untuk kontrol modal
    public bool $activityModal = false;

    public $searchActivities = null;

    // Aturan validasi
    protected function rules(): array
    {
        return [
            'teacher_ids'             => ['required', 'array', 'min:1'],
            'teacher_ids.*'           => ['exists:teachers,id'],
            'subject_id'              => ['required', 'exists:subjects,id'],
            'selectedStudentGroupIds' => ['required', 'array', 'min:1'],
            'selectedStudentGroupIds.*' => ['exists:student_groups,id'],
            'activity_tag_id'         => ['nullable', 'exists:activity_tags,id'],
            'practicum_sks'           => ['nullable', 'integer', 'min:1', 'max:10'],
            'name'                    => ['nullable', 'string', 'max:255'],
        ];
    }

    // Pesan validasi kustom
    protected function messages(): array
    {
        return [
            'teacher_ids.required'             => 'Setidaknya pilih satu dosen.',
            'subject_id.required'              => 'Mata kuliah wajib dipilih.',
            'selectedStudentGroupIds.required' => 'Setidaknya pilih satu kelompok mahasiswa.',
        ];
    }

    // Inisialisasi komponen
    public function mount(): void
    {
        $prodi = auth()->user()->prodi;

        if (!$prodi) {
            $this->teachers = $this->subjects = $this->allStudentGroups = $this->activityTags = collect();
            return;
        }

        $clusterTeacherIds = collect();
        if ($prodi->cluster_id) {
            $prodiIdsInCluster = Prodi::where('cluster_id', $prodi->cluster_id)->pluck('id');
            $clusterTeacherIds = DB::table('prodi_teacher')->whereIn('prodi_id', $prodiIdsInCluster)->pluck('teacher_id');
        }

        $linkedTeacherIds = $prodi->teachers()->pluck('teachers.id');
        $allTeacherIds = $clusterTeacherIds->merge($linkedTeacherIds)->unique();
        $this->teachers = Teacher::whereIn('id', $allTeacherIds)->orderBy('nama_dosen')->get();
        $this->subjects = Subject::where('prodi_id', $prodi->id)->orderBy('semester')->orderBy('kode_matkul')->get();
        $this->allStudentGroups = StudentGroup::where('prodi_id', $prodi->id)
            ->where(fn ($q) => $q->whereDoesntHave('children')->orWhere(fn ($sq) => $sq->whereHas('children')->whereDoesntHave('children.children')))
            ->orderBy('nama_kelompok')->get();
        $this->activityTags = ActivityTag::orderBy('name')->get();
    }

    public function toggleTeacherSelection(int $teacherId): void
    {
        $key = array_search($teacherId, $this->teacher_ids);
        if ($key !== false) {
            array_splice($this->teacher_ids, $key, 1);
        } else {
            $this->teacher_ids[] = $teacherId;
        }
    }

    // Mendefinisikan header tabel
    public function headers(): array
    {
        return [
            ['key' => 'subject.kode_matkul', 'label' => 'Kode', 'class' => 'w-16'],
            ['key' => 'subject_display', 'label' => 'Mata Kuliah'],
            ['key' => 'subject.semester', 'label' => 'SMT', 'class' => 'w-1 text-center'],
            ['key' => 'duration', 'label' => 'SKS Total', 'class' => 'w-1 text-center'],
            ['key' => 'activity_tag.name', 'label' => 'Tag', 'class' => 'w-1 text-center'],
            ['key' => 'student_group_names', 'label' => 'Kelompok'],
            ['key' => 'teacher_names', 'label' => 'Dosen Pengampu'],
            ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-1'],
        ];
    }

    // Merender tampilan dan data
    public function render(): View
    {
        $activities = Activity::join('subjects', 'subjects.id', '=', 'activities.subject_id')
            ->where('activities.prodi_id', auth()->user()->prodi_id)
            ->with(['teachers', 'subject', 'studentGroups', 'activityTag'])
            ->when($this->searchActivities, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('subjects.nama_matkul', 'like', "%{$search}%")
                        ->orWhere('teachers.nama_dosen', 'like', "%{$search}%");
                });
            })
            ->select('activities.*')
            ->groupBy('activities.id')
            ->orderBy('subjects.semester')
            ->orderBy('subjects.kode_matkul')
            ->orderBy('subjects.nama_matkul')
            ->paginate(10);

        return view('livewire.prodi.manage-activities', ['activities' => $activities, 'headers' => $this->headers(),])->layout('layouts.app');
    }

    // Membuka modal untuk membuat aktivitas baru
    public function create(): void
    {
        $this->resetInputFields();
        $this->activityModal = true;
    }

    // Menyimpan data (baik baru maupun update)
    public function store(): void
    {
        $validatedData = $this->validate();
        $subject = Subject::find($validatedData['subject_id']);

        // Logika "defensif" untuk memastikan SKS Praktikum dihitung dengan benar
        $teori_sks = $subject->sks ?? 0;
        $isPracticumSksEmpty = ($this->practicum_sks === '' || is_null($this->practicum_sks));
        $praktikum_sks = $isPracticumSksEmpty ? 0 : (int) $this->practicum_sks;
        $finalDuration = $teori_sks + $praktikum_sks;

        $activityData = [
            'subject_id'      => $validatedData['subject_id'],
            'activity_tag_id' => $validatedData['activity_tag_id'],
            'prodi_id'        => auth()->user()->prodi_id,
            'duration'        => $finalDuration,
            'practicum_sks'   => $isPracticumSksEmpty ? null : $praktikum_sks,
            'name'            => $validatedData['name'],
            'quantity'        => 1,
        ];

        $activity = Activity::updateOrCreate(['id' => $this->activityId], $activityData);

        // Sinkronisasi dosen dengan urutan
        $syncData = [];
        if (!empty($this->teacher_ids)) {
            foreach ($this->teacher_ids as $index => $teacherId) {
                $syncData[$teacherId] = ['order' => $index];
            }
        }
        $activity->teachers()->sync($syncData);
        $activity->studentGroups()->sync($validatedData['selectedStudentGroupIds']);

        $this->toast(type: 'success', title: $this->activityId ? 'Aktivitas berhasil diperbarui.' : 'Aktivitas berhasil ditambahkan.');
        $this->closeModal();
    }

    // Mengisi form dengan data untuk diedit
    public function edit(Activity $activity): void
    {
        if ($activity->prodi_id !== auth()->user()->prodi_id) {
            $this->toast(type: 'error', title: 'Akses Ditolak!');
            return;
        }
        $this->resetInputFields();
        $this->activityId = $activity->id;
        $this->teacher_ids = $activity->teachers->pluck('id')->all();
        $this->subject_id = $activity->subject_id;
        $this->selectedStudentGroupIds = $activity->studentGroups->pluck('id')->map(fn($id) => (string) $id)->all();
        $this->activity_tag_id = $activity->activity_tag_id;
        $this->name = $activity->name;

        $this->practicum_sks = $activity->practicum_sks > 0 ? $activity->practicum_sks : null;

        $this->activityModal = true;
    }

    // Menghapus aktivitas
    public function delete(Activity $activity): void
    {
        if ($activity->prodi_id !== auth()->user()->prodi_id) {
            $this->toast(type: 'error', title: 'Akses Ditolak!');
            return;
        }
        $activity->delete();
        $this->toast(type: 'warning', title: 'Aktivitas berhasil dihapus.');
    }

    // Menutup modal
    public function closeModal(): void
    {
        $this->activityModal = false;
        $this->resetInputFields();
    }

    // Membersihkan input form
    private function resetInputFields(): void
    {
        $this->reset([
            'activityId',
            'teacher_ids',
            'subject_id',
            'selectedStudentGroupIds',
            'activity_tag_id',
            'name',
            'practicum_sks'
        ]);

        $this->teacher_ids = [];
        $this->selectedStudentGroupIds = [];
        $this->resetErrorBag();
    }
}
