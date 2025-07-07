<?php

namespace App\Livewire\Mahasiswa;

use Livewire\Component;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $schedules;

    public function mount()
    {
        $user = Auth::user();

        // Cek jika user adalah mahasiswa dan punya kelompok
        if ($user->hasRole('mahasiswa') && $user->student_group_id) {
            // Ambil semua jadwal yang cocok dengan kelompok mahasiswa ini
            $this->schedules = Schedule::where('student_group_id', $user->student_group_id)
                ->with(['subject', 'teacher', 'room', 'timeSlot.day']) // Eager load untuk performa
                ->get()
                ->sortBy('timeSlot.day.id') // Urutkan berdasarkan hari
                ->groupBy('timeSlot.day.name'); // Kelompokkan berdasarkan nama hari
        } else {
            $this->schedules = collect(); // Koleksi kosong jika bukan mahasiswa
        }
    }

    public function render()
    {
        return view('livewire.mahasiswa.dashboard')->layout('layouts.app');
    }
}
