<?php

namespace App\Livewire\Fakultas;

use App\Models\Day;
use App\Models\MasterRuangan;
use App\Models\RoomTimeConstraint;
use App\Models\TimeSlot;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class ManageRoomConstraints extends Component
{
    public Collection $rooms;
    public Collection $days;
    public Collection $timeSlots;

    public ?int $selectedRoomId = null;
    public array $constraints = [];

    public function mount()
    {
        $this->rooms = MasterRuangan::orderBy('kode_ruangan')->get();
        $this->days = Day::orderBy('id')->get();
        $this->timeSlots = TimeSlot::orderBy('start_time')->get();
        $this->loadConstraints();
    }

    public function updatedSelectedRoomId($value)
    {
        $this->loadConstraints();
    }

    public function loadConstraints()
    {
        if ($this->selectedRoomId) {
            $this->constraints = RoomTimeConstraint::where('master_ruangan_id', $this->selectedRoomId)
                ->get()
                ->keyBy(fn($constraint) => $constraint->day_id . '-' . $constraint->time_slot_id)
                ->all();
        } else {
            $this->constraints = [];
        }
    }

    public function toggleConstraint($dayId, $timeSlotId)
    {
        if (!$this->selectedRoomId) {
            session()->flash('error', 'Silakan pilih ruangan terlebih dahulu.');
            return;
        }

        $key = $dayId . '-' . $timeSlotId;

        if (isset($this->constraints[$key])) {
            if($constraint = RoomTimeConstraint::find($this->constraints[$key]['id'])) {
                $constraint->delete();
                session()->flash('message', 'Batasan waktu berhasil dihapus.');
            }
        } else {
            RoomTimeConstraint::create([
                'master_ruangan_id' => $this->selectedRoomId,
                'day_id' => $dayId,
                'time_slot_id' => $timeSlotId,
            ]);
            session()->flash('message', 'Batasan waktu berhasil ditambahkan.');
        }

        $this->loadConstraints();
    }

    public function getCellClasses($dayId, $slotId): string
    {
        if (!$this->selectedRoomId) {
            return 'bg-gray-100 dark:bg-gray-800 cursor-not-allowed';
        }

        $key = $dayId . '-' . $slotId;
        $isConstrained = isset($this->constraints[$key]);

        $baseClasses = 'cursor-pointer transition-colors';
        $constrainedClasses = 'bg-red-200 dark:bg-red-800/60 hover:bg-red-300 dark:hover:bg-red-700';
        $availableClasses = 'bg-green-200 dark:bg-green-800/30 hover:bg-green-300 dark:hover:bg-green-700';

        return $baseClasses . ' ' . ($isConstrained ? $constrainedClasses : $availableClasses);
    }

    public function render()
    {
        return view('livewire.fakultas.manage-room-constraints')
            ->layout('layouts.app');
    }
}
