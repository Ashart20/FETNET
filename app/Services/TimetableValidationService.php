<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Day;
use App\Models\MasterRuangan;
use App\Models\StudentGroup;
use App\Models\Teacher;
use App\Models\TimeSlot;
use Illuminate\Support\Facades\DB;

/**
 * Service untuk memvalidasi integritas data penjadwalan
 * sebelum file .fet dibuat dan dijalankan oleh FET engine.
 */
class TimetableValidationService
{
    /**
     * @var array Menyimpan semua isu (error/warning) yang ditemukan.
     */
    private array $issues = [];

    /**
     * Menjalankan semua proses validasi dan mengembalikan hasilnya.
     *
     * @return array
     */
    public function validateAllData(): array
    {
        $this->validateActivities();
        $this->validateRoomCapacity();
        $this->validateUniqueIdentifiers();
        $this->validateTeacherWorkload();
        $this->validateLockedResources();
        $this->validateRoomSupplyVsDemand();
        $this->validateConstraintIntersection();
        // Anda bisa menambahkan method validasi lain di sini

        return $this->issues;
    }

    /**
     * Validasi 1: Memastikan setiap aktivitas memiliki komponen dasar.
     */
    private function validateActivities(): void
    {
        $activities = Activity::with(['teachers', 'subject', 'studentGroups'])->get();

        foreach ($activities as $activity) {
            if ($activity->teachers->isEmpty()) {
                $this->addIssue('Error', "Aktivitas '{$activity->nameOrSubject}' (ID: {$activity->id}) tidak memiliki dosen.", "Edit aktivitas dan tambahkan dosen pengampu.");
            }
            if ($activity->studentGroups->isEmpty()) {
                $this->addIssue('Error', "Aktivitas '{$activity->nameOrSubject}' (ID: {$activity->id}) tidak memiliki kelompok mahasiswa.", "Edit aktivitas dan tambahkan kelompok mahasiswa.");
            }
            if (!$activity->subject) {
                $this->addIssue('Error', "Aktivitas dengan ID: {$activity->id} tidak terhubung dengan mata kuliah.", "Hapus atau perbaiki aktivitas ini.");
            }
            if ($activity->duration <= 0) {
                $this->addIssue('Warning', "Aktivitas '{$activity->nameOrSubject}' (ID: {$activity->id}) memiliki durasi 0 atau kurang.", "Perbaiki SKS pada mata kuliah terkait atau SKS tambahan pada aktivitas.");
            }
        }
    }

    /**
     * Validasi 2: Memastikan kapasitas ruangan mencukupi untuk setiap aktivitas.
     */
    private function validateRoomCapacity(): void
    {
        $allRooms = \App\Models\MasterRuangan::all();
        // Tambahkan 'prodi' ke dalam with() untuk eager loading
        $activities = \App\Models\Activity::with('studentGroups', 'activityTag', 'prodi')->get();

        $theoryRoomsMaxCapacity = $allRooms->where('tipe', 'KELAS_TEORI')->max('kapasitas') ?? 0;
        $labRoomsMaxCapacity = $allRooms->where('tipe', 'LABORATORIUM')->max('kapasitas') ?? 0;

        foreach ($activities as $activity) {
            $studentCount = $activity->studentGroups->sum('jumlah_mahasiswa');
            if ($studentCount == 0) continue;

            // Siapkan informasi tambahan untuk pesan error
            $prodiName = $activity->prodi?->nama_prodi ?? 'N/A';
            $groupNames = $activity->studentGroups->pluck('nama_kelompok')->implode(', ');

            $tag = $activity->activityTag->name ?? 'KELAS TEORI';

            if ($tag === 'PRAKTIKUM') {
                if ($studentCount > $labRoomsMaxCapacity) {
                    // Pesan error baru yang lebih deskriptif
                    $message = "Aktivitas Praktikum '{$activity->nameOrSubject}' (Prodi: {$prodiName}, Kelompok: {$groupNames}) butuh kapasitas {$studentCount}.";
                    $suggestion = "Kapasitas lab terbesar hanya {$labRoomsMaxCapacity}. Tidak bisa dijadwalkan.";
                    $this->addIssue('Error', $message, $suggestion);
                }
            } else { // Untuk KELAS TEORI, PILIHAN, dll.
                if ($studentCount > $theoryRoomsMaxCapacity) {
                    // Pesan error baru yang lebih deskriptif
                    $message = "Aktivitas Teori '{$activity->nameOrSubject}' (Prodi: {$prodiName}, Kelompok: {$groupNames}) butuh kapasitas {$studentCount}.";
                    $suggestion = "Kapasitas ruang teori terbesar hanya {$theoryRoomsMaxCapacity}. Tidak bisa dijadwalkan.";
                    $this->addIssue('Error', $message, $suggestion);
                }
            }
        }
    }

    /**
     * Validasi 3: Memastikan identifier penting bersifat unik.
     */
    private function validateUniqueIdentifiers(): void
    {
        $duplicateTeachers = DB::table('teachers')->select('kode_dosen')->groupBy('kode_dosen')->havingRaw('COUNT(*) > 1')->pluck('kode_dosen');
        foreach ($duplicateTeachers as $kode) {
            $this->addIssue('Error', "Kode Dosen '{$kode}' digunakan oleh lebih dari satu dosen.", "Buka halaman Manajemen Dosen dan pastikan semua Kode Dosen unik.");
        }

        $duplicateRooms = DB::table('master_ruangans')->select('nama_ruangan')->groupBy('nama_ruangan')->havingRaw('COUNT(*) > 1')->pluck('nama_ruangan');
        foreach ($duplicateRooms as $nama) {
            $this->addIssue('Error', "Nama Ruangan '{$nama}' digunakan oleh lebih dari satu ruangan.", "Buka halaman Manajemen Ruangan dan pastikan semua Nama Ruangan unik.");
        }
    }

    /**
     * Validasi 4: Memastikan beban kerja dosen tidak melebihi waktu ketersediaannya.
     */
    private function validateTeacherWorkload(): void
    {
        $totalPossibleSlots = \App\Models\Day::count() * \App\Models\TimeSlot::count();
        if ($totalPossibleSlots === 0) return;

        // Tambahkan 'prodis' ke dalam with() untuk eager loading
        $teachers = \App\Models\Teacher::withSum('activities', 'duration')
            ->withCount('timeConstraints')
            ->with('prodis')
            ->get();

        foreach ($teachers as $teacher) {
            $totalLoad = $teacher->activities_sum_duration ?? 0;
            $totalUnavailable = $teacher->time_constraints_count;
            $totalAvailable = $totalPossibleSlots - $totalUnavailable;

            if ($totalLoad > $totalAvailable) {
                // Dapatkan daftar kode prodi yang terhubung dengan dosen ini
                $prodiNames = $teacher->prodis->pluck('nama_prodi')->implode(', ');

                // Buat pesan peringatan baru yang lebih informatif
                $message = "Beban SKS Dosen '{$teacher->nama_dosen}' (Prodi: {$prodiNames}) ({$totalLoad} SKS) melebihi waktu ketersediaannya ({$totalAvailable} slot).";
                $suggestion = "Periksa kembali batasan waktu atau alokasi mengajarnya.";

                $this->addIssue('Warning', $message, $suggestion);
            }
        }
    }

    /**
     * Validasi 5: Mencari sumber daya yang tidak mungkin digunakan karena terkunci 100%.
     */
    private function validateLockedResources(): void
    {
        $totalPossibleSlots = Day::count() * TimeSlot::count();
        if ($totalPossibleSlots === 0) return;

        // Cek Dosen yang terkunci
        $lockedTeachers = Teacher::withCount('timeConstraints')->having('time_constraints_count', '>=', $totalPossibleSlots)->get();
        foreach ($lockedTeachers as $teacher) {
            $this->addIssue('Error', "Dosen '{$teacher->nama_dosen}' tidak tersedia sama sekali.", "Semua slot waktunya ditutup. Buka halaman Batasan Waktu Dosen untuk memperbaikinya.");
        }

        // Cek Ruangan yang terkunci
        $lockedRooms = MasterRuangan::withCount('timeConstraints')->having('time_constraints_count', '>=', $totalPossibleSlots)->get();
        foreach ($lockedRooms as $room) {
            $this->addIssue('Error', "Ruangan '{$room->nama_ruangan}' tidak tersedia sama sekali.", "Semua slot waktunya ditutup. Buka halaman Batasan Waktu Ruangan untuk memperbaikinya.");
        }
    }
    private function validateRoomSupplyVsDemand(): void
    {
        // 1. Hitung total slot waktu yang mungkin dalam seminggu
        $totalPossibleSlots = Day::count() * TimeSlot::count();
        if ($totalPossibleSlots === 0) return;

        // 2. Hitung KETERSEDIAAN (SUPPLY) jam per tipe ruangan
        $roomSupply = [];
        $roomsByType = MasterRuangan::withCount('timeConstraints')->get()->groupBy('tipe');

        foreach ($roomsByType as $type => $rooms) {
            $totalSlotsForType = $rooms->count() * $totalPossibleSlots;
            $totalUnavailableSlots = $rooms->sum('time_constraints_count');
            $roomSupply[$type] = $totalSlotsForType - $totalUnavailableSlots;
        }

        // 3. Hitung KEBUTUHAN (DEMAND) jam per tipe aktivitas
        $activityDemand = Activity::with('activityTag')->get()
            ->groupBy(function ($activity) {
                // Kelompokkan berdasarkan tag, anggap 'KELAS TEORI' sebagai default
                return $activity->activityTag->name ?? 'KELAS TEORI';
            })
            ->map(fn ($activities) => $activities->sum('duration'));

        // 4. Bandingkan SUPPLY vs DEMAND untuk Praktikum
        $labDemand = $activityDemand->get('PRAKTIKUM', 0);
        $labSupply = $roomSupply['LABORATORIUM'] ?? 0;
        if ($labDemand > $labSupply) {
            $this->addIssue(
                'Error',
                "Total SKS Praktikum ({$labDemand} SKS) melebihi total jam laboratorium yang tersedia ({$labSupply} jam).",
                "Jadwal tidak mungkin dibuat. Kurangi aktivitas praktikum atau tambah ketersediaan laboratorium."
            );
        }

        // 5. Bandingkan SUPPLY vs DEMAND untuk Teori (dan lainnya)
        $theoryDemand = $activityDemand->except('PRAKTIKUM')->sum();
        $theorySupply = $roomSupply['KELAS_TEORI'] ?? 0;
        if ($theoryDemand > $theorySupply) {
            $this->addIssue(
                'Error',
                "Total SKS Teori ({$theoryDemand} SKS) melebihi total jam ruang kelas yang tersedia ({$theorySupply} jam).",
                "Jadwal tidak mungkin dibuat. Kurangi aktivitas teori atau tambah ketersediaan ruang kelas."
            );
        }
    }

    private function validateConstraintIntersection(): void
    {
        // Muat semua relasi yang dibutuhkan sekaligus
        $activities = \App\Models\Activity::with([
            'teachers.timeConstraints',
            'studentGroups.timeConstraints',
            'preferredRooms.timeConstraints',
            'prodi',
        ])->get();

        $totalPossibleSlots = \App\Models\Day::count() * \App\Models\TimeSlot::count();
        if ($totalPossibleSlots === 0) {
            return;
        }

        foreach ($activities as $activity) {
            // Kumpulkan slot tidak tersedia dari Dosen
            $teacherUnavailableSlots = $activity->teachers->flatMap(fn ($t) => $t->timeConstraints->map(fn ($c) => $c->day_id . '-' . $c->time_slot_id));

            // Kumpulkan slot tidak tersedia dari Mahasiswa
            $studentUnavailableSlots = $activity->studentGroups->flatMap(fn ($g) => $g->timeConstraints->map(fn ($c) => $c->day_id . '-' . $c->time_slot_id));

            // Gabungkan semua slot tidak tersedia dari Dosen dan Mahasiswa
            $peopleUnavailableSlots = $teacherUnavailableSlots->merge($studentUnavailableSlots)->unique();

            // Jika Dosen & Mahasiswa saja sudah tidak punya waktu bersama, langsung laporkan
            if ($peopleUnavailableSlots->count() >= $totalPossibleSlots) {

                // --- INI BAGIAN LENGKAPNYA ---
                $teacherNames = $activity->teachers->pluck('nama_dosen')->implode(', ');
                $groupNames = $activity->studentGroups->pluck('nama_kelompok')->implode(', ');

                $this->addIssue(
                    'Error',
                    "Aktivitas '{$activity->nameOrSubject}' (Prodi: {$activity->prodi?->nama_prodi}) tidak dapat dijadwalkan.",
                    "Tidak ada titik temu waktu luang antara Dosen ({$teacherNames}) dan Mahasiswa ({$groupNames})."
                );
                // --- AKHIR BAGIAN LENGKAP ---

                continue; // Lanjut ke aktivitas berikutnya, tidak perlu cek ruangan lagi
            }

            // --- LOGIKA CEK TITIK TEMU DENGAN RUANGAN ---
            if ($activity->preferredRooms->isNotEmpty()) {
                $allPossibleSlotsMap = [];
                $days = \App\Models\Day::all();
                $timeSlots = \App\Models\TimeSlot::all();
                foreach ($days as $day) {
                    foreach ($timeSlots as $timeSlot) {
                        $allPossibleSlotsMap[$day->id . '-' . $timeSlot->id] = true;
                    }
                }
                $possibleSlotsForPeople = collect(array_keys($allPossibleSlotsMap))->diff($peopleUnavailableSlots);

                $canBePlaced = false;
                foreach ($possibleSlotsForPeople as $slotKey) {
                    foreach ($activity->preferredRooms as $room) {
                        if (!$room->timeConstraints->contains(fn($c) => ($c->day_id . '-' . $c->time_slot_id) === $slotKey)) {
                            $canBePlaced = true;
                            break;
                        }
                    }
                    if ($canBePlaced) break;
                }

                if (!$canBePlaced) {
                    $this->addIssue(
                        'Error',
                        "Aktivitas '{$activity->nameOrSubject}' (Prodi: {$activity->prodi?->nama_prodi}) tidak dapat dijadwalkan.",
                        "Tidak ada titik temu waktu luang antara Dosen/Mahasiswa dengan Ruangan yang tersedia."
                    );
                }
            }
        }
    }
    /**
     * Helper untuk menambahkan isu ke dalam laporan.
     */
    private function addIssue(string $type, string $message, string $suggestion): void
    {
        $this->issues[] = [
            'type' => $type, // 'Error' atau 'Warning'
            'message' => $message,
            'suggestion' => $suggestion,
        ];
    }
}
