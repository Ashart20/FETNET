<?php

namespace App\Livewire\Prodi;

use App\Models\Activity;
use App\Models\ActivityTag;
use App\Models\MasterRuangan;
use App\Models\StudentGroup;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class ManageActivities extends Component
{
    use WithPagination;

    public Collection $teachers, $subjects, $studentGroups, $activityTags, $rooms;

    public ?int $activityId = null;
    public array $teacher_ids = [];
    public $subject_id = '';
    public $student_group_id = '';
    public $activity_tag_id = '';
    public array $preferred_room_ids = [];
    public bool $isModalOpen = false;

    protected function rules(): array
    {
        return [
            'teacher_ids'          => ['required', 'array', 'min:1'],
            'teacher_ids.*'        => ['exists:teachers,id'],
            'subject_id'           => ['required', 'exists:subjects,id'],
            'student_group_id'     => ['required', 'exists:student_groups,id'],
            'activity_tag_id'      => ['nullable', 'exists:activity_tags,id'],
            'preferred_room_ids'   => ['nullable', 'array'],
            'preferred_room_ids.*' => ['exists:master_ruangans,id'],
        ];
    }

    protected function messages(): array
    {
        return [
            'teacher_ids.required' => 'Setidaknya pilih satu dosen.',
            'subject_id.required' => 'Mata kuliah wajib dipilih.',
            'student_group_id.required' => 'Kelompok mahasiswa wajib dipilih.',
        ];
    }
    public function mount()
    {
        $prodiId = auth()->user()->prodi_id;
        $this->teachers = Teacher::where('prodi_id', $prodiId)->orderBy('nama_dosen')->get();
        $this->subjects = Subject::where('prodi_id', $prodiId)->orderBy('nama_matkul')->get();
        $this->studentGroups = StudentGroup::where('prodi_id', $prodiId)
            ->whereNotNull('parent_id')
            ->orderBy('nama_kelompok')
            ->get();
        $this->activityTags = ActivityTag::orderBy('name')->get();
        $this->rooms = MasterRuangan::orderBy('nama_ruangan')->get();
    }

    public function render()
    {
        $activities = Activity::where('prodi_id', auth()->user()->prodi_id)
            ->with(['teachers', 'subject', 'studentGroup', 'activityTag'])
            ->latest()
            ->paginate(10);

        return view('livewire.prodi.manage-activities', [
            'activities' => $activities
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    // app/Livewire/Prodi/ManageActivities.php

    public function store()
    {
        $validatedData = $this->validate();
        $subject = Subject::find($validatedData['subject_id']);
        $activityData = [
            'subject_id'       => $validatedData['subject_id'],
            'student_group_id' => $validatedData['student_group_id'],
            'activity_tag_id'  => $validatedData['activity_tag_id'] ?? null, // Gunakan null jika tidak ada
            'prodi_id'         => auth()->user()->prodi_id,
            'duration'         => $subject->sks, // Ambil durasi dari SKS
        ];

        $activity = Activity::updateOrCreate(['id' => $this->activityId], $activityData);
        $activity->teachers()->sync($validatedData['teacher_ids']);
        $activity->preferredRooms()->sync($validatedData['preferred_room_ids'] ?? []); // Gunakan array kosong jika tidak ada

        session()->flash('message', $this->activityId ? 'Aktivitas Berhasil Diperbarui.' : 'Aktivitas Berhasil Ditambahkan.');

        $this->closeModal();
    }
    public function edit($id)
    {
        $activity = Activity::with('teachers', 'preferredRooms')
            ->where('prodi_id', auth()->user()->prodi_id)
            ->findOrFail($id);

        $this->activityId = $id;
        $this->teacher_ids = $activity->teachers->pluck('id')->all();
        $this->subject_id = $activity->subject_id;
        $this->student_group_id = $activity->student_group_id;
        $this->activity_tag_id = $activity->activity_tag_id;
        $this->preferred_room_ids = $activity->preferredRooms->pluck('id')->all();

        $this->openModal();
    }

    public function delete($id)
    {
        // Periksa kepemilikan sebelum menghapus
        $activity = Activity::where('prodi_id', auth()->user()->prodi_id)->findOrFail($id);
        $activity->delete(); // Langsung delete dari model yang ditemukan
        session()->flash('message', 'Aktivitas Berhasil Dihapus.');
    }

    public function openModal()
    {
        $this->isModalOpen = true;
        $this->dispatch('open-modal-activity'); // Kirim event ke browser
    }

    public function closeModal() {
        $this->isModalOpen = false;
        $this->resetInputFields();
        $this->dispatch('close-modal');
    }

    private function resetInputFields()
    {
        // PERBAIKAN: Gunakan metode reset() Livewire yang lebih ringkas
        $this->reset('activityId', 'teacher_ids', 'subject_id', 'student_group_id', 'activity_tag_id', 'preferred_room_ids');
        $this->resetErrorBag();
    }
}
