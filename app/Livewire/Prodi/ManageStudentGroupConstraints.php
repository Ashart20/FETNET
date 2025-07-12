<?php

namespace App\Livewire\Prodi;

use App\Models\Day;
use App\Models\StudentGroup;
use App\Models\StudentGroupTimeConstraint;
use App\Models\TimeSlot;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Mary\Traits\Toast;

class ManageStudentGroupConstraints extends Component
{
    use Toast;

    public array $studentGroupsForDropdown = [];

    public Collection $days;
    public Collection $timeSlots;

    public ?int $selectedStudentGroupId = null;
    public array $constraints = [];

    // Properti untuk menyorot
    public ?int $highlightedDayId = null;
    public ?int $highlightedTimeSlotId = null;

    public function mount(): void
    {
        $prodiId = auth()->user()->prodi_id;

        if ($prodiId) {

            $topLevelGroups = StudentGroup::where('prodi_id', $prodiId)
                ->whereNull('parent_id')
                ->with('childrenRecursive')
                ->orderBy('nama_kelompok')
                ->get();


            $this->studentGroupsForDropdown = $this->flattenGroups($topLevelGroups);
        }

        $this->days = Day::orderBy('id')->get();
        $this->timeSlots = TimeSlot::orderBy('start_time')->get();
        $this->loadConstraints();
    }

    private function flattenGroups(Collection $groups, int $level = 0): array
    {
        $result = [];
        foreach ($groups as $group) {
            $result[] = [
                'id' => $group->id,
                'name' => str_repeat('---', $level) . ' ' . $group->nama_kelompok
            ];
            if ($group->childrenRecursive->isNotEmpty()) {
                $result = array_merge($result, $this->flattenGroups($group->childrenRecursive, $level + 1));
            }
        }
        return $result;
    }

    public function updatedSelectedStudentGroupId($value): void
    {
        $this->resetHighlight();
        $this->loadConstraints();
    }

    public function loadConstraints(): void
    {
        if ($this->selectedStudentGroupId) {
            $this->constraints = StudentGroupTimeConstraint::where('student_group_id', $this->selectedStudentGroupId)
                ->get()
                ->keyBy(fn($constraint) => $constraint->day_id . '-' . $constraint->time_slot_id)
                ->all();
        } else {
            $this->constraints = [];
        }
    }

    public function toggleConstraint($dayId, $timeSlotId): void
    {
        if (!$this->selectedStudentGroupId) {
            $this->error('Silakan pilih kelompok mahasiswa terlebih dahulu.');
            return;
        }
        $key = $dayId . '-' . $timeSlotId;
        if (isset($this->constraints[$key])) {
            StudentGroupTimeConstraint::destroy($this->constraints[$key]['id']);
            $this->success('Batasan waktu berhasil dihapus.');
        } else {
            StudentGroupTimeConstraint::create([
                'student_group_id' => $this->selectedStudentGroupId,
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

    public function setHighlightedDayUnavailable(): void
    {
        if (!$this->selectedStudentGroupId || !$this->highlightedDayId) return;
        foreach($this->timeSlots as $slot) {
            StudentGroupTimeConstraint::updateOrCreate(
                ['student_group_id' => $this->selectedStudentGroupId, 'day_id' => $this->highlightedDayId, 'time_slot_id' => $slot->id]
            );
        }
        $this->finalizeBatchAction('Semua slot waktu pada hari yang dipilih berhasil ditandai tidak tersedia.');
    }

    public function setHighlightedDayAvailable(): void
    {
        if (!$this->selectedStudentGroupId || !$this->highlightedDayId) return;
        StudentGroupTimeConstraint::where('student_group_id', $this->selectedStudentGroupId)
            ->where('day_id', $this->highlightedDayId)
            ->delete();
        $this->finalizeBatchAction('Semua batasan pada hari yang dipilih berhasil dikosongkan.');
    }

    public function setHighlightedTimeSlotUnavailable(): void
    {
        if (!$this->selectedStudentGroupId || !$this->highlightedTimeSlotId) return;
        foreach($this->days as $day) {
            StudentGroupTimeConstraint::updateOrCreate(
                ['student_group_id' => $this->selectedStudentGroupId, 'day_id' => $day->id, 'time_slot_id' => $this->highlightedTimeSlotId]
            );
        }
        $this->finalizeBatchAction('Semua hari pada jam yang dipilih berhasil ditandai tidak tersedia.');
    }

    public function setHighlightedTimeSlotAvailable(): void
    {
        if (!$this->selectedStudentGroupId || !$this->highlightedTimeSlotId) return;
        StudentGroupTimeConstraint::where('student_group_id', $this->selectedStudentGroupId)
            ->where('time_slot_id', $this->highlightedTimeSlotId)
            ->delete();
        $this->finalizeBatchAction('Semua batasan pada jam yang dipilih berhasil dikosongkan.');
    }

    public function resetHighlight(): void
    {
        $this->highlightedDayId = null;
        $this->highlightedTimeSlotId = null;
    }

    private function finalizeBatchAction(string $message): void
    {
        $this->loadConstraints();
        $this->resetHighlight();
        $this->success($message);
    }

    public function render(): View
    {
        return view('livewire.prodi.manage-student-group-constraints')
            ->layout('layouts.app');
    }
}
