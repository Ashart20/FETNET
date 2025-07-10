<?php

namespace App\Livewire\Prodi;

use App\Models\Day;
use App\Models\Teacher;
use App\Models\TeacherTimeConstraint;
use App\Models\TimeSlot;
use App\Models\Prodi; // Pastikan model Prodi diimpor
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Component;

class ManageTeacherConstraints extends Component
{
    public Collection $teachers;
    public Collection $days;
    public Collection $timeSlots;

    public ?int $selectedTeacherId = null;
    public array $constraints = [];

    public function mount(): void
    {
        $prodi = auth()->user()->prodi; // Ambil objek prodi

        if (!$prodi) {
            // Handle kasus jika user tidak terhubung dengan prodi (misal: user baru yang belum diatur)
            $this->teachers = collect();
            $this->days = Day::orderBy('id')->get();
            $this->timeSlots = TimeSlot::orderBy('start_time')->get();
            return;
        }

        // Logika pengambilan dosen berdasarkan cluster
        $teachersQuery = Teacher::query();
        if ($prodi->cluster_id) {
            // Jika prodi memiliki cluster, ambil semua dosen dari prodi di cluster yang sama
            $prodiIdsInCluster = Prodi::where('cluster_id', $prodi->cluster_id)->pluck('id');
            $teachersQuery->whereHas('prodis', function ($query) use ($prodiIdsInCluster) {
                $query->whereIn('prodis.id', $prodiIdsInCluster);
            });
        } else {
            // Jika prodi tidak memiliki cluster, hanya ambil dosen yang terkait langsung dengan prodi ini
            $teachersQuery->whereHas('prodis', function ($query) use ($prodi) {
                $query->where('prodis.id', $prodi->id);
            });
        }

        $this->teachers = $teachersQuery->distinct()->orderBy('nama_dosen')->get(); // Pastikan unik dan urut

        $this->days = Day::orderBy('id')->get();
        $this->timeSlots = TimeSlot::orderBy('start_time')->get();

        $this->loadConstraints();
    }

    public function updatedSelectedTeacherId($value): void
    {
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
            session()->flash('error', 'Silakan pilih dosen terlebih dahulu.');
            return;
        }

        // PENINGKATAN KEAMANAN: Pastikan user hanya bisa mengubah dosen di prodinya
        // Menggunakan $this->teachers Collection yang sudah difilter
        $teacher = $this->teachers->find($this->selectedTeacherId);
        if (!$teacher) {
            abort(403, 'Akses ditolak. Dosen tidak ditemukan atau tidak terkait dengan prodi Anda/cluster Anda.');
        }

        $key = $dayId . '-' . $timeSlotId;

        if (isset($this->constraints[$key])) {
            TeacherTimeConstraint::destroy($this->constraints[$key]['id']);
            session()->flash('message', 'Batasan waktu berhasil dihapus (dosen tersedia).');
        } else {
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
