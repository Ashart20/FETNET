<?php

namespace App\Livewire;

use App\Models\Schedule;
use Illuminate\Support\Facades\Log;
// Pastikan model yang relevan diimpor
use Livewire\Component;

class ScheduleConflictDetector extends Component
{
    // Properti untuk notifikasi umum
    public $showConflictNotification = false;

    public $showCleanNotification = false; // Ini untuk notifikasi "Tidak Ada Konflik!"

    // Properti untuk menyimpan detail konflik
    public $conflicts = []; // Pastikan ini dideklarasikan

    // Listeners untuk menerima event dari FetScheduleViewer
    protected $listeners = [
        'refreshConflictDetector' => 'detectConflicts',
        // Jika Anda memiliki event spesifik untuk menampilkan notifikasi konflik dari tempat lain
        // 'show-conflict-notification' => 'showConflictNotificationEvent',
        // 'clearConflictAlert' => 'clearConflictAlert', // Listener untuk tombol close
    ];

    // Method yang dipanggil saat mount komponen
    public function mount()
    {
        Log::info('ScheduleConflictDetector: Component mounted.');
        // detectConflicts() akan dipanggil setelah data jadwal utama dimuat/diperbarui
        // Kita tidak ingin memanggilnya di mount kecuali sangat diperlukan,
        // karena mount() FetScheduleViewer sudah akan memicu refreshConflictDetector
    }

    // Method ini akan dipanggil oleh event 'refreshConflictDetector'
    public function detectConflicts()
    {
        Log::info('ScheduleConflictDetector: Detecting conflicts...');
        $this->conflicts = []; // Bersihkan konflik sebelumnya
        $this->showConflictNotification = false; // Reset notifikasi konflik
        $this->showCleanNotification = false; // Reset notifikasi bersih

        $schedules = Schedule::with(['timeSlot', 'room'])->get();

        if ($schedules->isEmpty()) {
            Log::info('ScheduleConflictDetector: No schedules found to detect conflicts.');
            $this->showCleanNotification = true; // Tampilkan notifikasi bersih jika tidak ada jadwal
            $this->dispatch('showCleanNotification', 'Tidak ada jadwal yang terdeteksi.');

            return;
        }

        $teacherOccupancy = []; // Dosen -> Hari -> Jam -> [Activities]
        $roomOccupancy = [];    // Ruangan -> Hari -> Jam -> [Activities]
        $groupOccupancy = [];   // Kelas -> Hari -> Jam -> [Activities]

        foreach ($schedules as $schedule) {
            $day = optional($schedule->timeSlot)->day;
            $startTime = optional($schedule->timeSlot)->start_time;
            $endTime = optional($schedule->timeSlot)->end_time;
            $roomName = optional($schedule->room)->name;
            $teacher = $schedule->teacher;
            $kelas = $schedule->kelas;

            if (! $day || ! $startTime || ! $endTime) {
                Log::warning("ScheduleConflictDetector: Skipping schedule due to missing time slot data: ID {$schedule->id}");

                continue;
            }

            $currentSlot = $day.'-'.$startTime.'-'.$endTime;

            // Konflik Dosen
            if (isset($teacherOccupancy[$teacher][$currentSlot])) {
                $this->conflicts[] = [
                    'type' => 'Dosen Bentrok',
                    'resource' => $teacher,
                    'time' => "{$day}, {$startTime} - {$endTime}",
                    'sessions' => array_merge([$schedule->subject.' ('.$kelas.')'], array_map(function ($s) {
                        return $s->subject.' ('.$s->kelas.')';
                    }, $teacherOccupancy[$teacher][$currentSlot])),
                ];
            }
            $teacherOccupancy[$teacher][$currentSlot][] = $schedule;

            // Konflik Ruangan
            if ($roomName && isset($roomOccupancy[$roomName][$currentSlot])) {
                $this->conflicts[] = [
                    'type' => 'Ruangan Bentrok',
                    'resource' => $roomName,
                    'time' => "{$day}, {$startTime} - {$endTime}",
                    'sessions' => array_merge([$schedule->subject.' ('.$teacher.')'], array_map(function ($s) {
                        return $s->subject.' ('.$s->teacher.')';
                    }, $roomOccupancy[$roomName][$currentSlot])),
                ];
            }
            if ($roomName) { // Hanya tambahkan jika roomName valid
                $roomOccupancy[$roomName][$currentSlot][] = $schedule;
            }

            // Konflik Kelas
            if (isset($groupOccupancy[$kelas][$currentSlot])) {
                $this->conflicts[] = [
                    'type' => 'Kelas Bentrok',
                    'resource' => $kelas,
                    'time' => "{$day}, {$startTime} - {$endTime}",
                    'sessions' => array_merge([$schedule->subject.' ('.$teacher.')'], array_map(function ($s) {
                        return $s->subject.' ('.$s->teacher.')';
                    }, $groupOccupancy[$kelas][$currentSlot])),
                ];
            }
            $groupOccupancy[$kelas][$currentSlot][] = $schedule;
        }

        if (! empty($this->conflicts)) {
            $this->showConflictNotification = true;
            Log::warning('ScheduleConflictDetector: Conflicts detected: '.count($this->conflicts));
            // Mengirim event ke Alpine.js di frontend
            $this->dispatch('showConflictNotification', ['count' => count($this->conflicts)]);
        } else {
            $this->showCleanNotification = true;
            Log::info('ScheduleConflictDetector: No conflicts detected.');
            // Mengirim event ke Alpine.js di frontend
            $this->dispatch('showCleanNotification', 'Jadwal Anda bersih dari bentrokan.');
        }

        // Jika Anda ingin notifikasi sukses/bersih menghilang setelah beberapa saat,
        // logika setTimeout ada di blade.
    }

    public function clearConflictAlert()
    {
        $this->showConflictNotification = false;
        $this->showCleanNotification = false;
        $this->conflicts = []; // Hapus detail konflik juga
        Log::info('ScheduleConflictDetector: Conflict alert cleared by user.');
    }

    public function render()
    {
        return view('livewire.schedule-conflict-detector');
    }
}
