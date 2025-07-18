<?php

namespace App\Livewire\Fakultas;

use App\Models\Prodi;
use App\Models\Schedule;
use Livewire\Component;
use Livewire\WithPagination;

class ViewSchedules extends Component
{
    use WithPagination;

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
        $prodiId = $this->selectedProdiId;
        $schedulesByDay = collect();

        if ($prodiId) {
            // 1. Ambil data jadwal dari database
            $schedules = Schedule::query()
                ->with(['activity.subject', 'activity.teachers', 'activity.studentGroups', 'day', 'timeSlot', 'room'])
                ->whereHas('activity', function ($q) use ($prodiId) {
                    $q->where('prodi_id', $prodiId);
                })
                ->join('days', 'schedules.day_id', '=', 'days.id')
                ->join('time_slots', 'schedules.time_slot_id', '=', 'time_slots.id')
                ->orderBy('days.id')
                ->orderBy('schedules.activity_id')
                ->orderBy('time_slots.start_time')
                ->select('schedules.*')
                ->get();

            // 2. Kelompokkan berdasarkan hari
            $rawSchedulesByDay = $schedules->groupBy('day.id');

            // 3. Proses penggabungan dengan logika yang sudah diperbaiki
            foreach ($rawSchedulesByDay as $dayId => $daySchedules) {
                if ($daySchedules->isEmpty()) {
                    continue;
                }

                $merged = [];
                // Mulai blok pertama dengan membuat salinan (clone) yang dalam
                $currentBlock = clone $daySchedules->first();
                $currentBlock->setRelation('timeSlot', clone $currentBlock->timeSlot);

                // Loop dari jadwal kedua dan seterusnya
                for ($i = 1; $i < $daySchedules->count(); $i++) {
                    $nextSchedule = $daySchedules[$i];

                    // Cek jika jadwal berikutnya menyambung dengan blok saat ini
                    if (
                        $nextSchedule->activity_id == $currentBlock->activity_id &&
                        $nextSchedule->room_id == $currentBlock->room_id &&
                        strtotime($nextSchedule->timeSlot->start_time) == strtotime($currentBlock->timeSlot->end_time)
                    ) {
                        // Jika ya, cukup perpanjang jam selesai dari blok saat ini
                        $currentBlock->timeSlot->end_time = $nextSchedule->timeSlot->end_time;
                    } else {
                        // Jika tidak, simpan blok saat ini ke hasil
                        $merged[] = $currentBlock;

                        // Dan mulai blok baru dengan membuat salinan (clone) yang dalam
                        $currentBlock = clone $nextSchedule;
                        $currentBlock->setRelation('timeSlot', clone $currentBlock->timeSlot);
                    }
                }

                if ($currentBlock) {
                    $merged[] = $currentBlock;
                }

                $schedulesByDay[$daySchedules->first()->day->name] = collect($merged);
            }
        }

        return view('livewire.fakultas.view-schedules', [
            'schedules' => $schedulesByDay,
        ])->layout('layouts.app');
    }
}
