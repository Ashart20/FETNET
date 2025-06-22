<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Schedule;
use App\Models\Room;
use App\Models\TimeSlot;
use App\Services\FetWatchService;

class FetScheduleViewer extends Component
{
    use WithPagination;

    protected $listeners = ['scheduleDataUpdated' => 'refreshScheduleData'];

    public $filterHari = '';
    public $filterKelas = '';
    public $filterMatkul = '';
    public $filterRuangan = '';
    public $filterDosen = '';
    // HAPUS properti $apply. Ini tidak lagi dibutuhkan.
    // public $apply = false;

    // ... (properti daftarHari, daftarKelas, dll. dan filtersConfig tetap ada)
    public $daftarHari = [];
    public $daftarKelas = [];
    public $daftarMatkul = [];
    public $daftarRuangan = [];
    public $daftarDosen = [];
    public $filtersConfig = [];


    protected $queryString = [
        'filterHari',
        'filterKelas',
        'filterMatkul',
        'filterRuangan',
        'filterDosen',
    ];

    // Ubah applyFilter() agar hanya mereset paginasi
    public function applyFilter()
    {
        // $this->apply = true; // Hapus baris ini
        $this->resetPage(); // Hanya ini yang diperlukan. Ini akan memicu re-render
    }

    public function resetFilter()
    {
        $this->reset([
            'filterHari',
            'filterKelas',
            'filterMatkul',
            'filterRuangan',
            'filterDosen',
            // HAPUS 'apply' dari sini
            // 'apply'
        ]);
        $this->resetPage();
    }

    public function mount(FetWatchService $watcher)
    {
        $watcher->processAvailableFetFiles();
        $this->loadFilterOptions();
        $this->buildFiltersConfig();
    }

    public function refreshScheduleData()
    {
        $this->loadFilterOptions();
        $this->buildFiltersConfig();
        $this->dispatch('refreshConflictDetector');
    }

    public function loadFilterOptions()
    {
        // ... (tetap sama seperti sebelumnya)
        $getSortedUniqueValues = function($model, $column) {
            $values = $model::distinct()->pluck($column)
                ->map(fn($item) => trim($item))
                ->filter()
                ->toArray();
            sort($values);
            return $values;
        };

        $this->daftarKelas = $getSortedUniqueValues(Schedule::class, 'kelas');
        $this->daftarMatkul = $getSortedUniqueValues(Schedule::class, 'subject');
        $this->daftarRuangan = $getSortedUniqueValues(Room::class, 'name');
        $this->daftarDosen = $getSortedUniqueValues(Schedule::class, 'teacher');

        $hariMentah = $getSortedUniqueValues(TimeSlot::class, 'day');
        $urutanHariDefault = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        usort($hariMentah, function($a, $b) use ($urutanHariDefault) {
            $posA = array_search($a, $urutanHariDefault);
            $posB = array_search($b, $urutanHariDefault);

            if ($posA === false && $posB === false) return 0;
            if ($posA === false) return 1;
            if ($posB === false) return -1;

            return $posA <=> $posB;
        });
        $this->daftarHari = $hariMentah;
    }

    protected function buildFiltersConfig()
    {
        $this->filtersConfig = [
            ['label' => 'Hari', 'model' => 'filterHari', 'data_list' => $this->daftarHari],
            ['label' => 'Kelas', 'model' => 'filterKelas', 'data_list' => $this->daftarKelas],
            ['label' => 'Mata Kuliah', 'model' => 'filterMatkul', 'data_list' => $this->daftarMatkul],
            ['label' => 'Ruangan', 'model' => 'filterRuangan', 'data_list' => $this->daftarRuangan],
            ['label' => 'Dosen', 'model' => 'filterDosen', 'data_list' => $this->daftarDosen],
        ];
    }

    public function render()
    {
        $query = Schedule::with(['room', 'timeSlot']);

        // HAPUS conditional if ($this->apply) ini
        // if ($this->apply) {

        $query->when($this->filterHari, fn($q) =>
        $q->whereHas('timeSlot', fn($sub) => $sub->where('day', $this->filterHari))
        )
            ->when($this->filterKelas, fn($q) => $q->where('kelas', $this->filterKelas))
            ->when($this->filterMatkul, fn($q) => $q->where('subject', $this->filterMatkul))
            ->when($this->filterRuangan, fn($q) =>
            $q->whereHas('room', fn($sub) => $sub->where('name', $this->filterRuangan))
            )
            ->when($this->filterDosen, fn($q) => $q->where('teacher', $this->filterDosen));

        // HAPUS kurung kurawal penutup if ($this->apply)
        // }

        $jadwal = $query->paginate(10)->withQueryString();

        return view('livewire.fet-schedule-viewer', [
            'jadwal' => $jadwal,
        ]);
    }
}
