<?php

namespace App\Livewire;

use App\Events\ScheduleDataUpdatedEvent;
use App\Models\Day;
use App\Models\MasterRuangan;
use App\Models\Schedule;
use App\Models\StudentGroup;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class FetScheduleViewer extends Component
{
    use WithPagination;

    // Properti untuk filter
    public string $filterHari = '';

    public string $filterKelas = '';

    public string $filterMatkul = '';

    public string $filterRuangan = '';

    public string $filterDosen = '';

    // Properti untuk opsi dropdown
    public array $daftarHari = [];

    public array $daftarKelas = [];

    public array $daftarMatkul = [];

    public array $daftarRuangan = [];

    public array $daftarDosen = [];

    /**
     * Listener untuk event broadcast.
     */
    #[On(ScheduleDataUpdatedEvent::class)]
    public function refreshScheduleData(): void
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        $this->loadFilterOptions();
    }

    public function loadFilterOptions(): void
    {
        $user = Auth::user();
        $prodiId = $user?->prodi_id;

        $this->daftarHari = Day::orderBy('id')->pluck('name')->toArray();
        $this->daftarRuangan = MasterRuangan::orderBy('nama_ruangan')->pluck('nama_ruangan')->toArray();

        $this->daftarKelas = StudentGroup::when($prodiId, fn ($q) => $q->where('prodi_id', $prodiId))
            ->orderBy('nama_kelompok')
            ->pluck('nama_kelompok')
            ->toArray(); //
        $this->daftarMatkul = Subject::when($prodiId, fn ($q) => $q->where('prodi_id', $prodiId))->orderBy('nama_matkul')->pluck('nama_matkul')->toArray();
        $this->daftarDosen = Teacher::when($prodiId, function ($query) use ($prodiId) {
            $query->whereHas('prodis', function ($subQuery) use ($prodiId) {
                $subQuery->where('prodis.id', $prodiId);
            });
        })->orderBy('nama_dosen')->pluck('nama_dosen')->toArray();
    }

    /**
     * Hook ini berjalan setiap kali salah satu properti filter diubah.
     */
    public function updating($property): void
    {
        if (str_starts_with($property, 'filter')) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset('filterHari', 'filterKelas', 'filterMatkul', 'filterRuangan', 'filterDosen');
        $this->resetPage();
    }

    public function render(): View
    {
        $user = Auth::user();
        $prodiId = $user?->prodi_id;
        $studentGroupId = $user?->student_group_id;
        $query = Schedule::query()->with([
            'day', 'timeSlot', 'room', 'activity.subject', 'activity.studentGroups', 'activity.teachers', //
        ]);

        // Terapkan filter berdasarkan peran pengguna
        if ($user->hasRole('prodi') && $prodiId) {
            $query->whereHas('activity.subject', fn ($q) => $q->where('prodi_id', $prodiId));
        } elseif ($user->hasRole('mahasiswa') && $studentGroupId) {
            $query->whereHas('activity.studentGroups', fn ($q) => $q->where('id', $studentGroupId)); // Perubahan
        }

        // Terapkan filter dari dropdown
        $query->when($this->filterHari, fn ($q, $val) => $q->whereHas('day', fn ($sub) => $sub->where('name', $val)));
        $query->when($this->filterRuangan, fn ($q, $val) => $q->whereHas('room', fn ($sub) => $sub->where('nama_ruangan', $val)));
        $query->when($this->filterDosen, fn ($q, $val) => $q->whereHas('activity.teachers', fn ($sub) => $sub->where('nama_dosen', $val)));
        $query->when($this->filterKelas, fn ($q, $val) => $q->whereHas('activity.studentGroups', fn ($sub) => $sub->where('nama_kelompok', $val))); // Perubahan
        $query->when($this->filterMatkul, fn ($q, $val) => $q->whereHas('activity.subject', fn ($sub) => $sub->where('nama_matkul', $val)));

        $jadwal = $query->join('days', 'schedules.day_id', '=', 'days.id')
            ->join('time_slots', 'schedules.time_slot_id', '=', 'time_slots.id')
            ->orderBy('days.id')
            ->orderBy('time_slots.start_time')
            ->select('schedules.*')
            ->paginate(25);

        return view('livewire.fet-schedule-viewer', [
            'jadwal' => $jadwal,
        ])->layout('layouts.app');
    }
}
