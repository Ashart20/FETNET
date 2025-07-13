<?php

namespace App\Livewire\Cluster;

use App\Models\Activity;
use App\Models\ActivityTag;
use App\Models\Prodi;
use App\Models\StudentGroup;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class ManageActivities extends Component
{
    use WithPagination, Toast;

    // Properti untuk pilihan di form
    public Collection $prodis, $teachers, $subjects, $studentGroups, $activityTags;

    // Properti untuk form
    public ?int $activityId = null;
    public ?int $prodi_id = null;
    public array $teacher_ids = [];
    public ?int $subject_id = null;
    public array $selectedStudentGroupIds = []; // Menggunakan array untuk multi-select
    public ?int $activity_tag_id = null;
    public ?string $name = null;

    // Properti untuk kontrol modal
    public bool $activityModal = false;

    // TAMBAHAN: Properti untuk menyimpan data beban SKS
    public array $teacherSksLoad = [];

    protected function rules(): array
    {
        return [
            'prodi_id'         => ['required', 'exists:prodis,id'],
            'teacher_ids'      => ['required', 'array', 'min:1'],
            'teacher_ids.*'    => ['exists:teachers,id'],
            'subject_id'       => ['required', 'exists:subjects,id'],
            'selectedStudentGroupIds' => ['required', 'array', 'min:1'],
            'selectedStudentGroupIds.*' => ['exists:student_groups,id'],
            'activity_tag_id'  => ['nullable', 'exists:activity_tags,id'],
            'name'             => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function messages(): array
    {
        return [
            'prodi_id.required' => 'Program studi wajib dipilih.',
            'teacher_ids.required' => 'Setidaknya pilih satu dosen.',
            'subject_id.required' => 'Mata kuliah wajib dipilih.',
            'selectedStudentGroupIds.required' => 'Setidaknya pilih satu kelompok mahasiswa.',
        ];
    }

    public function mount(): void
    {
        $user = Auth::user();
        if (!$user->hasRole('cluster') || !$user->cluster_id) {
            $this->prodis = $this->teachers = $this->subjects = $this->studentGroups = $this->activityTags = collect();
            return;
        }

        $clusterId = $user->cluster_id;
        $prodiIdsInCluster = Prodi::where('cluster_id', $clusterId)->pluck('id');

        $this->prodis = Prodi::whereIn('id', $prodiIdsInCluster)->orderBy('nama_prodi')->get();
        $this->teachers = Teacher::whereHas('prodis', fn($q) => $q->whereIn('prodis.id', $prodiIdsInCluster))->distinct()->orderBy('nama_dosen')->get();
        $this->subjects = Subject::whereIn('prodi_id', $prodiIdsInCluster)->orderBy('nama_matkul')->get();
        $this->studentGroups = StudentGroup::whereIn('prodi_id', $prodiIdsInCluster)->whereDoesntHave('children')->orderBy('nama_kelompok')->get();
        $this->activityTags = ActivityTag::orderBy('name')->get();

        // Panggil method kalkulasi SKS saat komponen dimuat
        $this->calculateTeacherSksLoad();
    }

    public function calculateTeacherSksLoad(): void
    {
        $user = Auth::user();
        if (!$user->cluster_id) {
            $this->teacherSksLoad = [];
            return;
        }

        $prodiIdsInCluster = Prodi::where('cluster_id', $user->cluster_id)->pluck('id');
        $activities = Activity::whereIn('prodi_id', $prodiIdsInCluster)
            ->with(['teachers:id', 'subject:id,sks'])
            ->get();

        $sksPerTeacher = [];
        foreach ($activities as $activity) {
            $sks = $activity->subject->sks ?? 0;
            foreach ($activity->teachers as $teacher) {
                if (!isset($sksPerTeacher[$teacher->id])) {
                    $sksPerTeacher[$teacher->id] = 0;
                }
                $sksPerTeacher[$teacher->id] += $sks;
            }
        }
        $this->teacherSksLoad = $sksPerTeacher;
    }

    public function headers(): array
    {
        return [
            ['key' => 'prodi.nama_prodi', 'label' => 'Prodi'],
            ['key' => 'subject.nama_matkul', 'label' => 'Mata Kuliah'],
            ['key' => 'teacher_names', 'label' => 'Dosen'],
            ['key' => 'student_group_names', 'label' => 'Kelompok'],
            ['key' => 'duration', 'label' => 'Sesi', 'class' => 'w-1 text-center'],
        ];
    }

    public function render()
    {
        $this->calculateTeacherSksLoad(); // Pastikan data SKS selalu ter-update

        $clusterId = Auth::user()->cluster_id;
        $activitiesQuery = Activity::query();

        if ($clusterId) {
            $prodiIds = Prodi::where('cluster_id', $clusterId)->pluck('id');
            $activitiesQuery->whereIn('prodi_id', $prodiIds);
        } else {
            $activitiesQuery->where('id', -1);
        }

        return view('livewire.cluster.manage-activities', [
            'activities' => $activitiesQuery->with(['prodi', 'teachers', 'subject', 'studentGroups'])->latest()->paginate(10),
            'headers' => $this->headers(),
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->activityModal = true;
    }

    public function store()
    {
        $validatedData = $this->validate();
        $subject = Subject::find($validatedData['subject_id']);
        $activityData = [
            'prodi_id'         => $validatedData['prodi_id'],
            'subject_id'       => $validatedData['subject_id'],
            'activity_tag_id'  => $validatedData['activity_tag_id'] ?? null,
            'duration'         => $subject->sks,
            'name'             => $validatedData['name'] ?? null,
            'quantity'         => 1,
        ];

        $activity = Activity::updateOrCreate(['id' => $this->activityId], $activityData);
        $activity->teachers()->sync($validatedData['teacher_ids']);
        $activity->studentGroups()->sync($validatedData['selectedStudentGroupIds']);

        $this->toast(type: 'success', title: $this->activityId ? 'Aktivitas berhasil diperbarui.' : 'Aktivitas berhasil ditambahkan.');
        $this->closeModal();
    }

    public function edit(Activity $activity)
    {
        $this->activityId = $activity->id;
        $this->prodi_id = $activity->prodi_id;
        $this->name = $activity->name;
        $this->teacher_ids = $activity->teachers->pluck('id')->map(fn($id) => (string) $id)->all();
        $this->subject_id = $activity->subject_id;
        $this->selectedStudentGroupIds = $activity->studentGroups->pluck('id')->map(fn($id) => (string) $id)->all();
        $this->activity_tag_id = $activity->activity_tag_id;
        $this->activityModal = true;
    }

    public function delete(Activity $activity)
    {
        $clusterProdiIds = Auth::user()->cluster->prodis->pluck('id');
        if (!$clusterProdiIds->contains($activity->prodi_id)) {
            $this->toast(type: 'error', title: 'Aksi tidak diizinkan!');
            return;
        }
        $activity->delete();
        $this->toast(type: 'warning', title: 'Aktivitas berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->activityModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->reset('activityId', 'prodi_id', 'teacher_ids', 'subject_id', 'selectedStudentGroupIds', 'activity_tag_id', 'name');
        $this->resetErrorBag();
    }
}
