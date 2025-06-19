<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Schedule;
use App\Models\Room;

class FetScheduleViewer extends Component
{
    use WithPagination;

    public $filterHari = '';
    public $filterKelas = '';
    public $filterMatkul = '';
    public $filterRuangan = '';
    public $filterDosen = '';
    public $apply = false;

    protected $queryString = [
        'filterHari',
        'filterKelas',
        'filterMatkul',
        'filterRuangan',
        'filterDosen',
    ];

    public function applyFilter()
    {
        $this->apply = true;
        $this->resetPage();
    }

    public function resetFilter()
    {
        $this->reset([
            'filterHari',
            'filterKelas',
            'filterMatkul',
            'filterRuangan',
            'filterDosen',
            'apply'
        ]);
        $this->resetPage();
    }

    public function render()
    {
        $query = Schedule::with(['room', 'timeSlot']);

        if ($this->apply) {
            $query = $query
                ->when($this->filterHari, fn($q) =>
                $q->whereHas('timeSlot', fn($sub) => $sub->where('day', $this->filterHari))
                )
                ->when($this->filterKelas, fn($q) => $q->where('kelas', $this->filterKelas))
                ->when($this->filterMatkul, fn($q) => $q->where('subject', $this->filterMatkul))
                ->when($this->filterRuangan, fn($q) =>
                $q->whereHas('room', fn($sub) => $sub->where('name', $this->filterRuangan))
                )
                ->when($this->filterDosen, fn($q) => $q->where('teacher', $this->filterDosen));
        }

        $jadwal = $query->paginate(10)->withQueryString();

        return view('livewire.fet-schedule-viewer', [
            'jadwal' => $jadwal,
            'daftarHari' => Schedule::with('timeSlot')->get()->pluck('timeSlot.day')->unique()->filter()->values(),
            'daftarKelas' => Schedule::pluck('kelas')->unique()->filter()->values(),
            'daftarMatkul' => Schedule::pluck('subject')->unique()->filter()->values(),
            'daftarRuangan' => Room::pluck('name')->unique()->filter()->values(),
            'daftarDosen' => Schedule::pluck('teacher')->unique()->filter()->values(),
        ]);
    }
}
