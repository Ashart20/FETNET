<?php

namespace App\Livewire\Prodi;

use App\Models\Day;
use App\Models\Teacher;
use App\Models\TeacherTimeConstraint;
use App\Models\TimeSlot;
use App\Models\Prodi;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Mary\Traits\Toast;

class ManageTeacherConstraints extends Component
{
    use Toast;

    public Collection $teachers;
    public Collection $days;
    public Collection $timeSlots;

    public ?int $selectedTeacherId = null;
    public array $constraints = [];

    public ?int $highlightedDayId = null;
    public ?int $highlightedTimeSlotId = null;

    public function mount(): void
    {
        $prodi = auth()->user()->prodi;

        if (!$prodi) {
            $this->teachers = collect();
            $this->days = Day::orderBy('id')->get();
            $this->timeSlots = TimeSlot::orderBy('start_time')->get();
            return;
        }

        $teachersQuery = Teacher::query();
        if ($prodi->cluster_id) {
            $prodiIdsInCluster = Prodi::where('cluster_id', $prodi->cluster_id)->pluck('id');
            $teachersQuery->whereHas('prodis', function ($query) use ($prodiIdsInCluster) {
                $query->whereIn('prodis.id', $prodiIdsInCluster);
            });
        } else {
            $teachersQuery->whereHas('prodis', function ($query) use ($prodi) {
                $query->where('prodis.id', $prodi->id);
            });
        }

        $this->teachers = $teachersQuery->distinct()->orderBy('nama_dosen')->get();
        $this->days = Day::orderBy('id')->get();
        $this->timeSlots = TimeSlot::orderBy('start_time')->get();
        $this->loadConstraints();
    }

    public function updatedSelectedTeacherId($value): void
    {
        $this->resetHighlight();
        $this->loadConstraints();
    }

    public function loadConstraints(): void
    {
        if ($this->selectedTeacherId) {
            $this->constraints = TeacherTimeConstraint::where('teacher_id', $this->selectedTeacherId)
                ->get()
                ->keyBy(fn($constraint) => $constraint->day_id . '-' . $constraint->time_slot_id)
                ->all();
        } else {
            $this->constraints = [];
        }
    }

    public function toggleConstraint($dayId, $timeSlotId): void
    {
        if (!$this->selectedTeacherId) {
            $this->error('Silakan pilih dosen terlebih dahulu.');
            return;
        }

        $key = $dayId . '-' . $timeSlotId;

        if (isset($this->constraints[$key])) {
            TeacherTimeConstraint::destroy($this->constraints[$key]['id']);
            $this->success('Batasan waktu berhasil dihapus.');
        } else {
            TeacherTimeConstraint::create([
                'teacher_id' => $this->selectedTeacherId,
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

    /**
     * Menandai semua slot di kolom hari yang disorot sebagai TIDAK TERSEDIA.
     */
    public function setHighlightedDayUnavailable(): void
    {
        if (!$this->selectedTeacherId || !$this->highlightedDayId) return;

        foreach($this->timeSlots as $slot) {
            TeacherTimeConstraint::updateOrCreate(
                ['teacher_id' => $this->selectedTeacherId, 'day_id' => $this->highlightedDayId, 'time_slot_id' => $slot->id],
                []
            );
        }
        $this->finalizeBatchAction('Semua slot waktu pada hari yang dipilih berhasil ditandai tidak tersedia.');
    }

    /**
     * Menandai semua slot di kolom hari yang disorot sebagai TERSEDIA (menghapus batasan).
     */
    public function setHighlightedDayAvailable(): void
    {
        if (!$this->selectedTeacherId || !$this->highlightedDayId) return;

        TeacherTimeConstraint::where('teacher_id', $this->selectedTeacherId)
            ->where('day_id', $this->highlightedDayId)
            ->delete();

        $this->finalizeBatchAction('Semua batasan pada hari yang dipilih berhasil dikosongkan.');
    }

    /**
     * Menandai semua slot di baris waktu yang disorot sebagai TIDAK TERSEDIA.
     */
    public function setHighlightedTimeSlotUnavailable(): void
    {
        if (!$this->selectedTeacherId || !$this->highlightedTimeSlotId) return;

        foreach($this->days as $day) {
            TeacherTimeConstraint::updateOrCreate(
                ['teacher_id' => $this->selectedTeacherId, 'day_id' => $day->id, 'time_slot_id' => $this->highlightedTimeSlotId],
                []
            );
        }
        $this->finalizeBatchAction('Semua hari pada jam yang dipilih berhasil ditandai tidak tersedia.');
    }

    /**
     * Menandai semua slot di baris waktu yang disorot sebagai TERSEDIA (menghapus batasan).
     */
    public function setHighlightedTimeSlotAvailable(): void
    {
        if (!$this->selectedTeacherId || !$this->highlightedTimeSlotId) return;

        TeacherTimeConstraint::where('teacher_id', $this->selectedTeacherId)
            ->where('time_slot_id', $this->highlightedTimeSlotId)
            ->delete();

        $this->finalizeBatchAction('Semua batasan pada jam yang dipilih berhasil dikosongkan.');
    }

    /**
     * Mengosongkan sorotan.
     */
    public function resetHighlight(): void
    {
        $this->highlightedDayId = null;
        $this->highlightedTimeSlotId = null;
    }

    /**
     * Menyelesaikan aksi batch dengan memuat ulang data, mereset sorotan, dan mengirim notifikasi.
     */
    private function finalizeBatchAction(string $message): void
    {
        $this->loadConstraints();
        $this->resetHighlight();
        $this->success($message);
    }

    public function render(): View
    {
        return view('livewire.prodi.manage-teacher-constraints')
            ->layout('layouts.app');
    }
}
