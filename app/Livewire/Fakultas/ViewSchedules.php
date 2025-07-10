<?php

namespace App\Livewire\Fakultas;

use App\Models\Prodi;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ViewSchedules extends Component
{
    use WithPagination; // Aktifkan trait pagination

    public $prodis;
    public $selectedProdiId;

    public function mount()
    {
        $this->prodis = Prodi::orderBy('nama_prodi')->get();
        $this->selectedProdiId = $this->prodis->first()->id ?? null;
    }

    public function updatedSelectedProdiId()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Schedule::query()->with(['activity.subject', 'activity.teachers', 'activity.studentGroups', 'day', 'timeSlot', 'room']);

        if ($this->selectedProdiId) {
            $query->whereHas('activity', function ($q) {
                $q->where('prodi_id', $this->selectedProdiId);
            });
        }

        $schedules = $query->join('days', 'schedules.day_id', '=', 'days.id')
            ->join('time_slots', 'schedules.time_slot_id', '=', 'time_slots.id')
            ->orderBy('days.id')
            ->orderBy('time_slots.start_time')
            ->select('schedules.*')
            ->paginate(50);

        return view('livewire.fakultas.view-schedules', [
            'schedules' => $schedules,
        ])->layout('layouts.app');
    }
}
