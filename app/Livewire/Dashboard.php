<?php

namespace App\Livewire;

use App\Models\Room;
use App\Models\Schedule; // Import model Schedule
use App\Models\TimeSlot;     // Import model User (asumsi Anda memiliki model User)
use App\Models\User;     // Import model Room
use Livewire\Component;  // Import model TimeSlot

class Dashboard extends Component
{
    public function render()
    {
        // Statistik Jadwal
        $totalSchedules = Schedule::count();
        // Asumsi ada kolom 'is_active' di tabel schedules, jika tidak ada,
        // Anda perlu menentukan logika 'aktif' berdasarkan kriteria lain,
        // atau menghapus ini jika tidak relevan untuk skripsi.
        $activeSchedules = Schedule::where('is_active', true)->count();
        $inactiveSchedules = Schedule::where('is_active', false)->count();

        // Statistik Pengguna (asumsi User model adalah User sistem)
        $userCount = User::count(); // Total pengguna terdaftar (misal dari tabel users)

        // Ambil beberapa jadwal terkini (misal 5 jadwal terakhir yang masuk)
        // Pastikan relasi 'timeSlot' dan 'room' sudah di-eager load untuk tampilan di Blade
        $recentSchedules = Schedule::with(['timeSlot', 'room'])->orderBy('created_at', 'desc')->take(5)->get();

        // Statistik tambahan (opsional, dari data yang ada)
        $uniqueTeachers = Schedule::distinct()->count('teacher');
        $uniqueRooms = Schedule::distinct()->count('room_id'); // atau Room::count(); jika semua room sudah diinsert
        $uniqueClasses = Schedule::distinct()->count('kelas');

        return view('livewire.dashboard', [
            'totalSchedules' => $totalSchedules,
            'activeSchedules' => $activeSchedules,
            'inactiveSchedules' => $inactiveSchedules,
            'userCount' => $userCount,
            'recentSchedules' => $recentSchedules,
            'uniqueTeachers' => $uniqueTeachers,
            'uniqueRooms' => $uniqueRooms,
            'uniqueClasses' => $uniqueClasses,
        ]);
    }
}
