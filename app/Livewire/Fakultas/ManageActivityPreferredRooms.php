<?php

namespace App\Livewire\Fakultas;

use App\Models\Activity;
use App\Models\MasterRuangan;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class ManageActivityPreferredRooms extends Component
{
    use WithPagination, Toast;

    public bool $preferenceModal = false;
    public ?Activity $selectedActivity = null;
    public array $selectedRooms = [];
    public Collection $allRooms;

    public function mount(): void
    {
        $this->allRooms = MasterRuangan::orderBy('nama_ruangan')->get();
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID'],
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
            'selectedRooms'   => 'array',
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

        $activities = Activity::with(['subject', 'prodi', 'studentGroups', 'preferredRooms']) //
        ->latest()
            ->paginate(15);

        return view('livewire.fakultas.manage-activity-preferred-rooms', [
            'activities' => $activities,
        ])->layout('layouts.app');
    }
}
