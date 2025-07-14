<?php

namespace App\Livewire\Fakultas;

use App\Models\Schedule;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Livewire\Component;

class ConflictChecker extends Component
{
    public EloquentCollection $teachers;

    public ?string $selectedTeacherId = null;

    public function mount(): void
    {
        $this->teachers = Teacher::whereHas('schedules')->orderBy('nama_dosen')->get();
    }

    public function render()
    {
        $schedules = null;
        $hardConflicts = collect();
        $selectedTeacher = null;

        if ($this->selectedTeacherId) {
            $selectedTeacher = Teacher::find($this->selectedTeacherId);

            $schedules = Schedule::whereHas('teachers', function ($query) {
                $query->where('teachers.id', $this->selectedTeacherId);
            })
                ->with(['day', 'timeSlot', 'prodi', 'activity.subject', 'activity.studentGroups', 'room'])
                ->join('days', 'schedules.day_id', '=', 'days.id')
                ->join('time_slots', 'schedules.time_slot_id', '=', 'time_slots.id')
                ->orderBy('days.id')
                ->orderBy('time_slots.start_time')
                ->select('schedules.*')
                ->get();

            if ($schedules) {
                $hardConflicts = $schedules
                    ->groupBy(function ($schedule) {
                        // Kelompokkan berdasarkan kunci unik: ID Hari dan ID Slot Waktu
                        return $schedule->day_id.'-'.$schedule->time_slot_id;
                    })
                    ->filter(fn ($group) => $group->count() > 1); // Hanya ambil grup yang isinya lebih dari 1
            }
        }

        return view('livewire.fakultas.conflict-checker', [
            'schedules' => $schedules,
            'hardConflicts' => $hardConflicts,
            'selectedTeacher' => $selectedTeacher,
        ])->layout('layouts.app');
    }
}
