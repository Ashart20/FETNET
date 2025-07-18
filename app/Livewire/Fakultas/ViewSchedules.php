<?php

namespace App\Livewire\Fakultas;

use App\Models\Prodi;
use App\Models\Schedule;
use Livewire\Component;
use Livewire\WithPagination;

class ViewSchedules extends Component
{
    use WithPagination;

    // Aktifkan trait pagination

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
        $schedules = collect();
        $mergedSchedules = collect();

        if ($this->selectedProdiId) {
            $query = Schedule::query()
                ->with(['activity.subject', 'activity.teachers', 'activity.studentGroups', 'day', 'timeSlot', 'room'])
                ->whereHas('activity', function ($q) {
                    $q->where('prodi_id', $this->selectedProdiId);
                });

            // --- PERUBAHAN UTAMA ADA DI `orderBy` DI BAWAH INI ---
            $schedules = $query->join('days', 'schedules.day_id', '=', 'days.id')
                ->join('time_slots', 'schedules.time_slot_id', '=', 'time_slots.id')
                ->orderBy('days.id')
                ->orderBy('schedules.activity_id') // Memastikan semua jadwal aktivitas yang sama berkumpul
                ->orderBy('time_slots.start_time') // Baru diurutkan berdasarkan jam
                ->select('schedules.*')
                ->get();
            // --- AKHIR PERUBAHAN ---

            $schedulesByDay = $schedules->groupBy('day.name');

            foreach ($schedulesByDay as $day => $daySchedules) {
                $mergedDaySchedules = $daySchedules->reduce(function ($carry, $schedule) {
                    $lastSchedule = $carry->last();

                    if (
                        $lastSchedule &&
                        $lastSchedule->activity_id == $schedule->activity_id &&
                        $lastSchedule->room_id == $schedule->room_id &&
                        strtotime($schedule->timeSlot->start_time) == strtotime($lastSchedule->timeSlot->end_time)
                    ) {
                        $lastSchedule->timeSlot->end_time = $schedule->timeSlot->end_time;
                    } else {
                        $carry->push(clone $schedule);
                    }

                    return $carry;
                }, collect());

                $mergedSchedules[$day] = $mergedDaySchedules;
            }
        }

        return view('livewire.fakultas.view-schedules', [
            'schedules' => $mergedSchedules,
        ])->layout('layouts.app');
    }
}
