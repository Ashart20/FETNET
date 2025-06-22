<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Schedule;
use Carbon\Carbon;
use App\Models\Room;

class ScheduleConflictDetector extends Component
{
    public $schedules = [];
    public $conflicts = [];
    public $showCleanNotification = false;
    public $showConflictNotification = false; // <-- Properti baru untuk notifikasi konflik

    // Listener untuk event dari komponen lain
    protected $listeners = ['refreshConflictDetector' => 'loadSchedulesAndDetectConflicts'];

    public function mount()
    {
        $this->loadSchedulesAndDetectConflicts();
    }

    public function loadSchedules()
    {
        $this->schedules = Schedule::with('timeSlot', 'room')->get();
    }

    public function loadSchedulesAndDetectConflicts()
    {
        $this->loadSchedules();
        $this->detectConflicts();
    }

    public function detectConflicts()
    {
        $this->conflicts = []; // Reset konflik sebelum deteksi ulang
        $this->showCleanNotification = false; // Reset notifikasi bersih
        $this->showConflictNotification = false; // Reset notifikasi konflik

        $groupedSchedules = $this->schedules->filter(fn($s) => $s->timeSlot)->groupBy('timeSlot.day');

        foreach ($groupedSchedules as $day => $schedulesOnDay) {
            $count = $schedulesOnDay->count();

            for ($i = 0; $i < $count; $i++) {
                for ($j = $i + 1; $j < $count; $j++) {
                    $session1 = $schedulesOnDay[$i];
                    $session2 = $schedulesOnDay[$j];

                    if (!$session1->timeSlot || !$session2->timeSlot) {
                        continue;
                    }

                    $start1 = Carbon::parse($session1->timeSlot->start_time);
                    $end1   = Carbon::parse($session1->timeSlot->end_time);
                    $start2 = Carbon::parse($session2->timeSlot->start_time);
                    $end2   = Carbon::parse($session2->timeSlot->end_time);

                    if ($start1->lessThan($end2) && $start2->lessThan($end1)) {
                        if ($session1->room_id !== null && $session1->room_id === $session2->room_id && $session1->room && $session2->room) {
                            $this->conflicts[] = [
                                'type' => 'Ruangan',
                                'resource' => $session1->room->name,
                                'time' => $session1->timeSlot->day . ', ' . $session1->timeSlot->start_time . '-' . $session1->timeSlot->end_time,
                                'sessions' => [$session1->subject . ' (' . $session1->kelas . ')', $session2->subject . ' (' . $session2->kelas . ')'],
                            ];
                        }

                        if (!empty($session1->kode_dosen) && $session1->kode_dosen === $session2->kode_dosen) {
                            $this->conflicts[] = [
                                'type' => 'Guru',
                                'resource' => $session1->teacher,
                                'time' => $session1->timeSlot->day . ', ' . $session1->timeSlot->start_time . '-' . $session1->timeSlot->end_time,
                                'sessions' => [$session1->subject . ' (' . $session1->kelas . ')', $session2->subject . ' (' . $session2->kelas . ')'],
                            ];
                        }

                        if (!empty($session1->kelas) && $session1->kelas === $session2->kelas) {
                            $this->conflicts[] = [
                                'type' => 'Kelas',
                                'resource' => $session1->kelas,
                                'time' => $session1->timeSlot->day . ', ' . $session1->timeSlot->start_time . '-' . $session1->timeSlot->end_time,
                                'sessions' => [$session1->subject . ' (' . $session1->teacher . ')', $session2->subject . ' (' . $session2->teacher . ')'],
                            ];
                        }
                    }
                }
            }
        }

        // --- Logika Baru untuk Notifikasi ---
        if (!empty($this->conflicts)) {
            // Jika ada konflik, tampilkan notifikasi konflik
            $this->showConflictNotification = true;
            $this->dispatch('show-conflict-notification', ['count' => count($this->conflicts)]); // Kirim data ke JS
        } else {
            // Jika tidak ada konflik, tampilkan notifikasi "Bersih" sebentar
            $this->showCleanNotification = true;
            $this->dispatch('hide-clean-notification-after-delay'); // Panggil JS untuk menyembunyikan
        }
        // Pastikan session flash selalu di-manage oleh Livewire
        session()->forget('conflict_alert'); // Hapus session flash lama
    }

    public function clearConflictAlert()
    {
        $this->showConflictNotification = false; // Sembunyikan notifikasi konflik dari properti
        session()->forget('conflict_alert'); // Hapus juga dari session flash (opsional, tapi aman)
    }

    public function hideCleanNotification()
    {
        $this->showCleanNotification = false;
    }

    public function render()
    {
        return view('livewire.schedule-conflict-detector');
    }
}
