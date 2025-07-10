<?php

namespace App\Livewire\Prodi;

use App\Models\Activity;
use App\Models\ActivityTag;
use App\Models\Prodi;
use App\Models\StudentGroup;
use App\Models\Subject;
use App\Models\Teacher;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Illuminate\Database\Eloquent\Collection;

class ManageActivities extends Component
{
    use WithPagination, Toast;

    // Koleksi data untuk pilihan di form
    public Collection $teachers, $subjects, $studentGroups, $activityTags;

    // Properti untuk form
    public ?int $activityId = null;
    public array $teacher_ids = [];
    public ?int $subject_id = null;
    public ?int $student_group_id = null;
    public ?int $activity_tag_id = null;

    // Properti untuk kontrol modal
    public bool $activityModal = false;

    protected function rules(): array
    {
        return [
            'teacher_ids'      => ['required', 'array', 'min:1'],
            'teacher_ids.*'    => ['exists:teachers,id'],
            'subject_id'       => ['required', 'exists:subjects,id'],
            'student_group_id' => ['required', 'exists:student_groups,id'],
            'activity_tag_id'  => ['nullable', 'exists:activity_tags,id'],
        ];
    }

    protected function messages(): array
    {
        return [
            'teacher_ids.required'      => 'Setidaknya pilih satu dosen.',
            'subject_id.required'       => 'Mata kuliah wajib dipilih.',
            'student_group_id.required' => 'Kelompok mahasiswa wajib dipilih.',
        ];
    }

    public function mount()
    {
        $prodi = auth()->user()->prodi;

        if (!$prodi) {
            $this->teachers = $this->subjects = $this->studentGroups = $this->activityTags = collect();
            return;
        }

        // Ambil data dosen berdasarkan cluster
        if ($prodi->cluster_id) {
            $prodiIdsInCluster = Prodi::where('cluster_id', $prodi->cluster_id)->pluck('id');
            $this->teachers = Teacher::whereHas('prodis', fn($q) => $q->whereIn('prodis.id', $prodiIdsInCluster))
                ->distinct()->orderBy('nama_dosen')->get();
        } else {
            $this->teachers = $prodi->teachers()->orderBy('nama_dosen')->get();
        }

        // Ambil data lain yang dibutuhkan
        $this->subjects = Subject::where('prodi_id', $prodi->id)->orderBy('nama_matkul')->get();
        $this->studentGroups = StudentGroup::where('prodi_id', $prodi->id)->whereNotNull('parent_id')->orderBy('nama_kelompok')->get();
        $this->activityTags = ActivityTag::orderBy('name')->get();
    }

    // Mendefinisikan header untuk tabel Mary UI
    public function headers(): array
    {
        return [
            ['key' => 'teacher_names', 'label' => 'Dosen'],
            ['key' => 'subject.nama_matkul', 'label' => 'Mata Kuliah'],
            ['key' => 'studentGroup.nama_kelompok', 'label' => 'Kelompok'],
            ['key' => 'duration', 'label' => 'Sesi', 'class' => 'w-1 text-center'],
        ];
    }

    public function render()
    {
        $activities = Activity::where('prodi_id', auth()->user()->prodi_id)
            ->with(['teachers', 'subject', 'studentGroup'])
            ->latest()
            ->paginate(10);

        return view('livewire.prodi.manage-activities', [
            'activities' => $activities,
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
            'subject_id'       => $validatedData['subject_id'],
            'student_group_id' => $validatedData['student_group_id'],
            'activity_tag_id'  => $validatedData['activity_tag_id'] ?? null,
            'prodi_id'         => auth()->user()->prodi_id,
            'duration'         => $subject->sks,
        ];

        $activity = Activity::updateOrCreate(['id' => $this->activityId], $activityData);
        $activity->teachers()->sync($validatedData['teacher_ids']);

        $this->toast(type: 'success', title: $this->activityId ? 'Aktivitas berhasil diperbarui.' : 'Aktivitas berhasil ditambahkan.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $activity = Activity::with('teachers')->where('prodi_id', auth()->user()->prodi_id)->findOrFail($id);
        $this->activityId = $id;
        $this->teacher_ids = $activity->teachers->pluck('id')->map(fn($id) => (string) $id)->all();
        $this->subject_id = $activity->subject_id;
        $this->student_group_id = $activity->student_group_id;
        $this->activity_tag_id = $activity->activity_tag_id;
        $this->activityModal = true;
    }

    public function delete($id)
    {
        Activity::where('prodi_id', auth()->user()->prodi_id)->findOrFail($id)->delete();
        $this->toast(type: 'warning', title: 'Aktivitas berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->activityModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->reset('activityId', 'teacher_ids', 'subject_id', 'student_group_id', 'activity_tag_id');
        $this->resetErrorBag();
    }
}
