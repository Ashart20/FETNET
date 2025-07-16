<?php

namespace App\Livewire\Fakultas;

use App\Models\Activity;
use App\Models\FetNet\ClientLevel;
use App\Models\MasterRuangan;
use App\Models\Prodi;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class ManageActivityPreferredRooms extends Component
{
    use Toast, WithPagination;

    public bool $preferenceModal = false;

    public ?Activity $selectedActivity = null;

    public array $selectedRooms = [];

    public Collection $allRooms;
    public $search;
    public $prodi_searchable_id = null;
    public $prodisSearchable;
    public function mount(): void
    {
        $this->allRooms = MasterRuangan::orderBy('nama_ruangan')->get();
        $this->clientLevelsSelect();
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'class' => 'hidden'],
            ['key' => 'subject.nama_matkul', 'label' => 'Mata Kuliah'],
            ['key' => 'prodi.nama_prodi', 'label' => 'Prodi'],
            ['key' => 'student_group_names', 'label' => 'Kelompok Mahasiswa'], // PERUBAHAN: Gunakan key baru
            ['key' => 'preferred_rooms', 'label' => 'Preferensi Ruangan', 'sortable' => false],
            ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-1'],
        ];
    }

    public function getStudentGroupNamesAttribute(Activity $activity): string
    {
        return $activity->studentGroups->pluck('nama_kelompok')->implode(', ');
    }

    public function editPreferences(Activity $activity): void
    {
        // PERUBAHAN: Pastikan studentGroups juga dimuat saat edit
        $this->selectedActivity = $activity->load('studentGroups'); //
        $this->selectedRooms = $activity->preferredRooms()->pluck('master_ruangan_id')->toArray();
        $this->preferenceModal = true;
    }

    public function savePreferences(): void
    {
        $this->validate([
            'selectedRooms' => 'array',
            'selectedRooms.*' => 'exists:master_ruangans,id',
        ]);

        if ($this->selectedActivity) {
            $this->selectedActivity->preferredRooms()->sync($this->selectedRooms);
            $this->success('Preferensi ruangan berhasil diperbarui.');
            $this->closeModal();
        }
    }

    public function closeModal(): void
    {
        $this->preferenceModal = false;
        $this->reset('selectedActivity', 'selectedRooms');
    }

    public function render()
    {

        $activities = Activity:://
            where('prodi_id', $this->prodi_searchable_id)
            ->with(['subject', 'prodi', 'studentGroups', 'preferredRooms'])
            ->orderBy('prodi_id')
            ->orderBy('subject_id')
            ->paginate(9);

        return view('livewire.fakultas.manage-activity-preferred-rooms', [
            'activities' => $activities,
        ])->layout('layouts.app');
    }

    public function clientLevelsSelect(string $value = '')
    {
        // Besides the search results, you must include on demand selected option
        $selectedOption = Prodi::where('id', $this->prodi_searchable_id)->get();
        //$this->faculties = $selectedOption;
        $this->prodisSearchable = Prodi::query()
            ->where('kode', 'like', "%$value%")
            ->orwhere('nama_prodi', 'like', "%$value%")
            ->take(20)
            ->get()
            ->merge($selectedOption);     // <-- Adds selected option
    }


}
