<?php

namespace App\Livewire\Fakultas;

use App\Models\Day;
use App\Models\MasterRuangan;
use App\Models\RoomTimeConstraint;
use App\Models\TimeSlot;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Mary\Traits\Toast;

class ManageRoomConstraints extends Component
{
    use Toast;

    public Collection $rooms;

    public Collection $days;

    public Collection $timeSlots;

    public ?int $selectedRoomId = null;

    public array $constraints = [];

    // Properti untuk menyorot
    public ?int $highlightedDayId = null;

    public ?int $highlightedTimeSlotId = null;

    public function mount()
    {
        $this->rooms = MasterRuangan::orderBy('kode_ruangan')->get();
        $this->days = Day::orderBy('id')->get();
        $this->timeSlots = TimeSlot::orderBy('start_time')->get();
        $this->loadConstraints();
    }

    public function updatedSelectedRoomId($value)
    {
        $this->resetHighlight();
        $this->loadConstraints();
    }

    public function loadConstraints()
    {
        if ($this->selectedRoomId) {
            $this->constraints = RoomTimeConstraint::where('master_ruangan_id', $this->selectedRoomId)
                ->get()
                ->keyBy(fn ($constraint) => $constraint->day_id.'-'.$constraint->time_slot_id)
                ->all();
        } else {
            $this->constraints = [];
        }
    }

    public function toggleConstraint($dayId, $timeSlotId)
    {
        if (! $this->selectedRoomId) {
            $this->error('Silakan pilih ruangan terlebih dahulu.');

            return;
        }

        $key = $dayId.'-'.$timeSlotId;

        if (isset($this->constraints[$key])) {
            if ($constraint = RoomTimeConstraint::find($this->constraints[$key]['id'])) {
                $constraint->delete();
                $this->success('Batasan waktu berhasil dihapus.');
            }
        } else {
            RoomTimeConstraint::create([
                'master_ruangan_id' => $this->selectedRoomId,
                'day_id' => $dayId,
                'time_slot_id' => $timeSlotId,
            ]);
            $this->success('Batasan waktu berhasil ditambahkan.');
        }

        $this->resetHighlight();
        $this->loadConstraints();
    }

    public function highlightDay($dayId): void
    {
        $this->highlightedTimeSlotId = null;
        $this->highlightedDayId = $this->highlightedDayId == $dayId ? null : $dayId;
    }

    public function highlightTimeSlot($timeSlotId): void
    {
        $this->highlightedDayId = null;
        $this->highlightedTimeSlotId = $this->highlightedTimeSlotId == $timeSlotId ? null : $timeSlotId;
    }

    public function setHighlightedDayUnavailable(): void
    {
        if (! $this->selectedRoomId || ! $this->highlightedDayId) {
            return;
        }
        foreach ($this->timeSlots as $slot) {
            RoomTimeConstraint::updateOrCreate(
                ['master_ruangan_id' => $this->selectedRoomId, 'day_id' => $this->highlightedDayId, 'time_slot_id' => $slot->id]
            );
        }
        $this->finalizeBatchAction('Semua slot waktu pada hari yang dipilih berhasil ditandai tidak tersedia.');
    }

    public function setHighlightedDayAvailable(): void
    {
        if (! $this->selectedRoomId || ! $this->highlightedDayId) {
            return;
        }
        RoomTimeConstraint::where('master_ruangan_id', $this->selectedRoomId)
            ->where('day_id', $this->highlightedDayId)
            ->delete();
        $this->finalizeBatchAction('Semua batasan pada hari yang dipilih berhasil dikosongkan.');
    }

    public function setHighlightedTimeSlotUnavailable(): void
    {
        if (! $this->selectedRoomId || ! $this->highlightedTimeSlotId) {
            return;
        }
        foreach ($this->days as $day) {
            RoomTimeConstraint::updateOrCreate(
                ['master_ruangan_id' => $this->selectedRoomId, 'day_id' => $day->id, 'time_slot_id' => $this->highlightedTimeSlotId]
            );
        }
        $this->finalizeBatchAction('Semua hari pada jam yang dipilih berhasil ditandai tidak tersedia.');
    }

    public function setHighlightedTimeSlotAvailable(): void
    {
        if (! $this->selectedRoomId || ! $this->highlightedTimeSlotId) {
            return;
        }
        RoomTimeConstraint::where('master_ruangan_id', $this->selectedRoomId)
            ->where('time_slot_id', $this->highlightedTimeSlotId)
            ->delete();
        $this->finalizeBatchAction('Semua batasan pada jam yang dipilih berhasil dikosongkan.');
    }

    public function resetHighlight(): void
    {
        $this->highlightedDayId = null;
        $this->highlightedTimeSlotId = null;
    }

    private function finalizeBatchAction(string $message): void
    {
        $this->loadConstraints();
        $this->resetHighlight();
        $this->success($message);
    }

    public function render()
    {
        return view('livewire.fakultas.manage-room-constraints')
            ->layout('layouts.app');
    }
}
