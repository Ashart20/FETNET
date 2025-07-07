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

        // Memuat batasan di awal (jika ada ID yang sudah dipilih sebelumnya)
        $this->loadConstraints();
    }

    // Hook ini akan otomatis berjalan setiap kali $selectedRoomId di-update oleh wire:model.live
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

        // PERBAIKAN: Cek dari array properti, bukan query ke DB
        if (isset($this->constraints[$key])) {
            // Hapus jika ada
            RoomTimeConstraint::find($this->constraints[$key]['id'])->delete();
            session()->flash('message', 'Batasan waktu berhasil dihapus.');
        } else {
            // Buat jika tidak ada
            RoomTimeConstraint::create([
                'master_ruangan_id' => $this->selectedRoomId,
                'day_id' => $dayId,
                'time_slot_id' => $timeSlotId,
            ]);
            session()->flash('message', 'Batasan waktu berhasil ditambahkan.');
        }

        // Muat ulang data batasan setelah diubah
        $this->loadConstraints();
    }

    public function render()
    {
        return view('livewire.fakultas.manage-room-constraints')
            ->layout('layouts.app');
    }
}
