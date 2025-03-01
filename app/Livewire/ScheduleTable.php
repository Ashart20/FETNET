<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Schedule;
use Illuminate\Support\Facades\Log;

class ScheduleTable extends Component
{
    protected $listeners = ['schedule-updates' => '$refresh'];

    public function render()
    {
        $schedules = Schedule::latest()->get();
        Log::info('Livewire refresh:', ['count' => $schedules->count()]); // Log jumlah data
        return view('livewire.schedule-table', compact('schedules'));
    }
}
