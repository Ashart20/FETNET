<?php

namespace App\Livewire\Prodi;

use App\Models\Day;
use App\Models\StudentGroup;
use App\Models\StudentGroupTimeConstraint;
use App\Models\TimeSlot;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Component;

class ManageStudentGroupConstraints extends Component
{
    public Collection $studentGroups;
    public Collection $days;
    public Collection $timeSlots;

    public ?int $selectedStudentGroupId = null;
    public array $constraints = [];

    public function mount(): void
    {
        $prodiId = auth()->user()->prodi_id;
        // Ambil data kelompok secara hierarkis untuk dropdown
        $this->studentGroups = StudentGroup::where('prodi_id', $prodiId)
            ->whereNull('parent_id')
            ->with('childrenRecursive')
            ->orderBy('nama_kelompok')
            ->get();

        $this->days = Day::orderBy('id')->get();
        $this->timeSlots = TimeSlot::orderBy('start_time')->get();

        $this->loadConstraints();
    }

    public function updatedSelectedStudentGroupId($value): void
    {
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
            session()->flash('error', 'Silakan pilih kelompok mahasiswa terlebih dahulu.');
            return;
        }

        $group = StudentGroup::where('prodi_id', auth()->user()->prodi_id)->find($this->selectedStudentGroupId);
        if (!$group) {
            abort(403, 'Akses ditolak.');
        }

        $key = $dayId . '-' . $timeSlotId;

        if (isset($this->constraints[$key])) {
            StudentGroupTimeConstraint::destroy($this->constraints[$key]['id']);
            session()->flash('message', 'Batasan waktu berhasil dihapus.');
        } else {
            StudentGroupTimeConstraint::create([
                'student_group_id' => $this->selectedStudentGroupId,
                'day_id' => $dayId,
                'time_slot_id' => $timeSlotId,
            ]);
            session()->flash('message', 'Batasan waktu berhasil ditambahkan.');
        }

        $this->loadConstraints();
    }

    public function render(): View
    {
        return view('livewire.prodi.manage-student-group-constraints')
            ->layout('layouts.app');
    }
}
