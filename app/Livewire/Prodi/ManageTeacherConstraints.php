<?php

namespace App\Livewire\Prodi;

use App\Models\Day;
use App\Models\Teacher;
use App\Models\TeacherTimeConstraint;
use App\Models\TimeSlot;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Component;

class ManageTeacherConstraints extends Component
{
    // Type-hinting untuk kejelasan kode
    public Collection $teachers;
    public Collection $days;
    public Collection $timeSlots;

    public ?int $selectedTeacherId = null;
    public array $constraints = [];

    public function mount(): void
    {
        $prodiId = auth()->user()->prodi_id;
        $this->teachers = Teacher::whereHas('prodis', function ($query) use ($prodiId) {
            $query->where('prodis.id', $prodiId);
        })->orderBy('nama_dosen')->get();
        $this->days = Day::orderBy('id')->get(); // Pastikan urutan hari benar
        $this->timeSlots = TimeSlot::orderBy('start_time')->get();

        $this->loadConstraints();
    }

    // Hook yang berjalan saat $selectedTeacherId berubah
    public function updatedSelectedTeacherId($value): void
    {
        $this->loadConstraints();
    }

    public function loadConstraints(): void
    {
        if ($this->selectedTeacherId) {
            // PERBAIKAN: Gunakan keyBy() yang lebih ringkas
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
            session()->flash('error', 'Silakan pilih dosen terlebih dahulu.');
            return;
        }

        // PENINGKATAN KEAMANAN: Pastikan user hanya bisa mengubah dosen di prodinya
        $teacher = $this->teachers->find($this->selectedTeacherId);
        if (!$teacher) {
            abort(403, 'Akses ditolak.');
        }

        $key = $dayId . '-' . $timeSlotId;

        // PERBAIKAN UTAMA: Cek dari array, bukan query database
        if (isset($this->constraints[$key])) {
            // Hapus constraint yang ada menggunakan ID-nya
            TeacherTimeConstraint::destroy($this->constraints[$key]['id']);
            session()->flash('message', 'Batasan waktu berhasil dihapus (dosen tersedia).');
        } else {
            // Buat constraint baru
            TeacherTimeConstraint::create([
                'teacher_id' => $this->selectedTeacherId,
                'day_id' => $dayId,
                'time_slot_id' => $timeSlotId,
            ]);
            session()->flash('message', 'Batasan waktu berhasil ditambahkan (dosen tidak tersedia).');
        }

        $this->loadConstraints();
    }

    public function render(): View
    {
        return view('livewire.prodi.manage-teacher-constraints')
            ->layout('layouts.app');
    }
}
