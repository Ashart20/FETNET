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
        $this->validateActivityDurationVsTeacherDailyLimit();
        $this->validateActivityDurationVsStudentDailyLimit();
        $this->validateSingleRoomWorkload();
        $this->validateOrphanRecords();
        $this->validateStudentWorkload();
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
        $activities = \App\Models\Activity::with('studentGroups', 'activityTag', 'preferredRooms', 'prodi')->get();
        foreach ($activities as $activity) {
            $studentCount = $activity->studentGroups->sum('jumlah_mahasiswa');
            if ($studentCount == 0) continue;

            // Tentukan kandidat ruangan: preferensi dulu, baru tipe umum
            $candidateRooms = $activity->preferredRooms;
            if ($candidateRooms->isEmpty()) {
                $tag = $activity->activityTag->name ?? 'KELAS TEORI';
                $roomType = ($tag === 'PRAKTIKUM') ? 'LABORATORIUM' : 'KELAS_TEORI';
                $candidateRooms = \App\Models\MasterRuangan::where('tipe', $roomType)->get();
            }

            $maxCapacityInSelection = $candidateRooms->max('kapasitas') ?? 0;

            if ($studentCount > $maxCapacityInSelection) {
                $prodiName = $activity->prodi?->nama_prodi ?? 'N/A';
                $groupNames = $activity->studentGroups->pluck('nama_kelompok')->implode(', ');

                $message = "Kapasitas ruangan tidak cukup untuk '{$activity->nameOrSubject}' (Prodi: {$prodiName}).";
                $suggestion = "Aktivitas ini (Kelompok: {$groupNames}) butuh kapasitas {$studentCount}, tetapi pilihan ruangan terbesar hanya {$maxCapacityInSelection}.";

                $this->addIssue('Error', $message, $suggestion);
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
        // 1. Muat semua relasi yang dibutuhkan sekaligus untuk performa
        $activities = \App\Models\Activity::with([
            'teachers.timeConstraints',
            'studentGroups.timeConstraints',
            'preferredRooms.timeConstraints',
            'prodi',
            'activityTag'
        ])->get();

        $totalPossibleSlots = \App\Models\Day::count() * \App\Models\TimeSlot::count();
        if ($totalPossibleSlots === 0) {
            return;
        }

        // Ambil semua tipe ruangan sekali saja untuk efisiensi
        $allRooms = \App\Models\MasterRuangan::with('timeConstraints')->get();
        $labRooms = $allRooms->where('tipe', 'LABORATORIUM');
        $theoryRooms = $allRooms->where('tipe', 'KELAS_TEORI');

        // 2. Loop melalui setiap aktivitas untuk divalidasi
        foreach ($activities as $activity) {
            // --- Tahap 1: Cek Titik Temu Dosen & Mahasiswa ---

            // Kumpulkan slot tidak tersedia dari Dosen
            $teacherUnavailableSlots = $activity->teachers->flatMap(fn ($t) => $t->timeConstraints->map(fn ($c) => $c->day_id . '-' . $c->time_slot_id));

            // Kumpulkan slot tidak tersedia dari Mahasiswa
            $studentUnavailableSlots = $activity->studentGroups->flatMap(fn ($g) => $g->timeConstraints->map(fn ($c) => $c->day_id . '-' . $c->time_slot_id));

            // Gabungkan semua slot tidak tersedia dari Dosen dan Mahasiswa
            $peopleUnavailableSlots = $teacherUnavailableSlots->merge($studentUnavailableSlots)->unique();

            // Jika Dosen & Mahasiswa saja sudah tidak punya waktu bersama, langsung laporkan dan hentikan pengecekan untuk aktivitas ini
            if ($peopleUnavailableSlots->count() >= $totalPossibleSlots) {
                $teacherNames = $activity->teachers->pluck('nama_dosen')->implode(', ');
                $groupNames = $activity->studentGroups->pluck('nama_kelompok')->implode(', ');

                $this->addIssue(
                    'Error',
                    "Aktivitas '{$activity->nameOrSubject}' (Prodi: {$activity->prodi?->nama_prodi}) tidak dapat dijadwalkan.",
                    "Tidak ada titik temu waktu luang antara Dosen ({$teacherNames}) dan Mahasiswa ({$groupNames})."
                );
                continue; // Lanjut ke aktivitas berikutnya
            }

            // --- Tahap 2: Cek Titik Temu dengan Ruangan ---

            // Tentukan kandidat ruangan untuk aktivitas ini
            $candidateRooms = $activity->preferredRooms;
            if ($candidateRooms->isEmpty()) {
                $tag = $activity->activityTag->name ?? 'KELAS TEORI';
                $candidateRooms = ($tag === 'PRAKTIKUM') ? $labRooms : $theoryRooms;
            }

            if ($candidateRooms->isEmpty()) continue;

            // Dapatkan semua slot waktu di mana dosen & mahasiswa BISA bertemu
            $allSlotsMap = [];
            $days = \App\Models\Day::all();
            $timeSlots = \App\Models\TimeSlot::all();
            foreach ($days as $day) {
                foreach ($timeSlots as $timeSlot) {
                    $allSlotsMap[$day->id . '-' . $timeSlot->id] = true;
                }
            }
            $possibleTimeKeys = collect(array_keys($allSlotsMap))->diff($peopleUnavailableSlots);

            // Untuk setiap waktu luang tersebut, cek apakah ada minimal 1 ruangan yang juga luang
            $isPlaceable = false;
            foreach ($possibleTimeKeys as $timeKey) {
                foreach ($candidateRooms as $room) {
                    $isRoomUnavailable = $room->timeConstraints->contains(fn ($c) => ($c->day_id . '-' . $c->time_slot_id) === $timeKey);
                    if (!$isRoomUnavailable) {
                        $isPlaceable = true; // Ditemukan kombinasi waktu & ruangan yang pas!
                        break;
                    }
                }
                if ($isPlaceable) break;
            }

            // Jika setelah semua pengecekan tidak ditemukan kombinasi yang pas
            if (!$isPlaceable) {
                $teacherNames = $activity->teachers->pluck('nama_dosen')->implode(', ');
                $groupNames = $activity->studentGroups->pluck('nama_kelompok')->implode(', ');

                $this->addIssue(
                    'Error',
                    "Aktivitas '{$activity->nameOrSubject}' (Prodi: {$activity->prodi?->nama_prodi}) tidak dapat dijadwalkan.",
                    "Tidak ada ruangan yang tersedia pada waktu luang bersama antara Dosen ({$teacherNames}) dan Mahasiswa ({$groupNames})."
                );
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
    private function validateActivityDurationVsTeacherDailyLimit(): void
    {
        // Ambil data yang dibutuhkan sekali saja untuk performa
        $teachers = \App\Models\Teacher::with(['activities', 'timeConstraints', 'prodis'])->get();
        $slotsInADay = \App\Models\TimeSlot::count();
        $days = \App\Models\Day::all();

        if ($slotsInADay === 0) return;

        // Loop melalui setiap dosen
        foreach ($teachers as $teacher) {
            // 1. Cari durasi (SKS) terbesar dari semua aktivitas yang diajar dosen ini
            $maxActivityDuration = $teacher->activities->max('duration');

            // Jika dosen tidak punya aktivitas, lewati
            if (!$maxActivityDuration) {
                continue;
            }

            // 2. Hitung slot tidak tersedia per hari untuk dosen ini
            $unavailableSlotsByDay = $teacher->timeConstraints->groupBy('day_id')->map->count();

            // 3. Cari hari dengan waktu luang terbanyak
            $maxAvailableSlotsOnAnyDay = 0;
            foreach ($days as $day) {
                $unavailableCount = $unavailableSlotsByDay->get($day->id, 0);
                $availableSlots = $slotsInADay - $unavailableCount;
                if ($availableSlots > $maxAvailableSlotsOnAnyDay) {
                    $maxAvailableSlotsOnAnyDay = $availableSlots;
                }
            }

            // 4. Bandingkan SKS terbesar dengan waktu luang terpanjang
            if ($maxActivityDuration > $maxAvailableSlotsOnAnyDay) {
                $prodiNames = $teacher->prodis->pluck('nama_prodi')->implode(', ');

                $this->addIssue(
                    'Error',
                    "Dosen '{$teacher->nama_dosen}' (Prodi: {$prodiNames}) memiliki aktivitas yang tidak bisa dijadwalkan.",
                    "Aktivitas terbesar butuh {$maxActivityDuration} jam, tetapi waktu luang terpanjang dalam sehari hanya {$maxAvailableSlotsOnAnyDay} jam."
                );
            }
        }
    }
    private function validateActivityDurationVsStudentDailyLimit(): void
    {
        // Ambil data yang dibutuhkan
        $studentGroups = \App\Models\StudentGroup::with(['activities', 'timeConstraints', 'prodi'])->get();
        $slotsInADay = \App\Models\TimeSlot::count();
        $days = \App\Models\Day::all();

        if ($slotsInADay === 0) return;

        // Loop melalui setiap kelompok mahasiswa
        foreach ($studentGroups as $group) {
            // 1. Cari durasi (SKS) terbesar dari semua aktivitas yang diikuti kelompok ini
            $maxActivityDuration = $group->activities->max('duration');

            if (!$maxActivityDuration) {
                continue;
            }

            // 2. Hitung slot tidak tersedia per hari untuk kelompok ini
            $unavailableSlotsByDay = $group->timeConstraints->groupBy('day_id')->map->count();

            // 3. Cari hari dengan waktu luang terbanyak
            $maxAvailableSlotsOnAnyDay = 0;
            foreach ($days as $day) {
                $unavailableCount = $unavailableSlotsByDay->get($day->id, 0);
                $availableSlots = $slotsInADay - $unavailableCount;
                if ($availableSlots > $maxAvailableSlotsOnAnyDay) {
                    $maxAvailableSlotsOnAnyDay = $availableSlots;
                }
            }

            // 4. Bandingkan SKS terbesar dengan waktu luang terpanjang
            if ($maxActivityDuration > $maxAvailableSlotsOnAnyDay) {
                $this->addIssue(
                    'Error',
                    "Kelompok '{$group->nama_kelompok}' (Prodi: {$group->prodi?->nama_prodi}) memiliki aktivitas yang tidak bisa dijadwalkan.",
                    "Aktivitas terbesar butuh {$maxActivityDuration} jam, tetapi waktu luang terpanjang dalam sehari hanya {$maxAvailableSlotsOnAnyDay} jam."
                );
            }
        }
    }
    private function validateSingleRoomWorkload(): void
    {
        $totalPossibleSlots = \App\Models\Day::count() * \App\Models\TimeSlot::count();
        if ($totalPossibleSlots === 0) return;

        // 1. Ambil semua aktivitas yang memiliki preferensi ruangan
        $activitiesWithPrefs = \App\Models\Activity::has('preferredRooms')->with('preferredRooms')->get();

        // 2. Filter hanya aktivitas yang punya SATU preferensi ruangan, lalu kelompokkan berdasarkan ID ruangan tersebut
        $activitiesBySingleRoom = $activitiesWithPrefs
            ->filter(fn ($activity) => $activity->preferredRooms->count() === 1)
            ->groupBy(fn ($activity) => $activity->preferredRooms->first()->id);

        // 3. Loop melalui setiap ruangan yang diperebutkan
        foreach ($activitiesBySingleRoom as $roomId => $activities) {
            // Hitung KEBUTUHAN (Demand): total SKS dari semua aktivitas yang memperebutkan ruangan ini
            $demand = $activities->sum('duration');

            // Hitung KETERSEDIAAN (Supply): total jam kosong untuk ruangan ini
            $room = \App\Models\MasterRuangan::withCount('timeConstraints')->find($roomId);
            if (!$room) continue;

            $supply = $totalPossibleSlots - $room->time_constraints_count;

            // 4. Bandingkan
            if ($demand > $supply) {
                $activityNames = $activities->pluck('nameOrSubject')->implode(', ');
                $this->addIssue(
                    'Error',
                    "Ruangan '{$room->nama_ruangan}' kelebihan beban.",
                    "Dibutuhkan total {$demand} jam oleh aktivitas ({$activityNames}), tetapi ruangan ini hanya tersedia {$supply} jam."
                );
            }
        }
    }
    private function validateOrphanRecords(): void
    {
        // Cek batasan waktu dosen yang tidak memiliki relasi dosen yang valid
        $orphanTeacherConstraints = \App\Models\TeacherTimeConstraint::whereDoesntHave('teacher')->get();
        foreach ($orphanTeacherConstraints as $constraint) {
            $this->addIssue(
                'Error',
                "Data Batasan Waktu Dosen (ID: {$constraint->id}) merujuk pada dosen yang sudah dihapus.",
                "Hapus data batasan ini melalui halaman Batasan Waktu Dosen atau langsung dari database."
            );
        }

        // Cek batasan waktu mahasiswa yang tidak memiliki relasi kelompok yang valid
        $orphanStudentConstraints = \App\Models\StudentGroupTimeConstraint::whereDoesntHave('studentGroup')->get();
        foreach ($orphanStudentConstraints as $constraint) {
            $this->addIssue(
                'Error',
                "Data Batasan Waktu Mahasiswa (ID: {$constraint->id}) merujuk pada kelompok yang sudah dihapus.",
                "Hapus data batasan ini melalui halaman Batasan Waktu Mahasiswa atau langsung dari database."
            );
        }

        // Cek batasan waktu ruangan yang tidak memiliki relasi ruangan yang valid
        $orphanRoomConstraints = \App\Models\RoomTimeConstraint::whereDoesntHave('masterRuangan')->get();
        foreach ($orphanRoomConstraints as $constraint) {
            $this->addIssue(
                'Error',
                "Data Batasan Waktu Ruangan (ID: {$constraint->id}) merujuk pada ruangan yang sudah dihapus.",
                "Hapus data batasan ini melalui halaman Batasan Waktu Ruangan atau langsung dari database."
            );
        }
        // Cek aktivitas yang tidak punya mata kuliah
        $orphanActivitiesBySubject = \App\Models\Activity::whereDoesntHave('subject')->get();
        foreach ($orphanActivitiesBySubject as $activity) {
            $this->addIssue('Error', "Aktivitas (ID: {$activity->id}) merujuk pada mata kuliah yang sudah dihapus.", "Perbaiki atau hapus aktivitas ini di halaman 'Manajemen Aktivitas'.");
        }

        // Cek aktivitas yang tidak punya prodi
        $orphanActivitiesByProdi = \App\Models\Activity::whereDoesntHave('prodi')->get();
        foreach ($orphanActivitiesByProdi as $activity) {
            $this->addIssue('Error', "Aktivitas (ID: {$activity->id}) merujuk pada prodi yang sudah dihapus.", "Perbaiki atau hapus aktivitas ini.");
        }

        // Cek mata kuliah yang tidak punya prodi
        $orphanSubjects = \App\Models\Subject::whereDoesntHave('prodi')->get();
        foreach ($orphanSubjects as $subject) {
            $this->addIssue('Error', "Mata Kuliah '{$subject->nama_matkul}' merujuk pada prodi yang sudah dihapus.", "Perbaiki atau hapus mata kuliah ini di halaman 'Manajemen Matkul'.");
        }
    }
    private function validateStudentWorkload(): void
    {
        // Hitung total slot waktu yang mungkin dalam seminggu
        $totalPossibleSlots = \App\Models\Day::count() * \App\Models\TimeSlot::count();
        if ($totalPossibleSlots === 0) {
            return;
        }

        // Ambil data kelompok, hanya dengan SKS total dan batasan waktu miliknya sendiri
        $studentGroups = \App\Models\StudentGroup::withSum('activities', 'duration')
            ->withCount('timeConstraints')
            ->with('prodi')
            ->get();

        foreach ($studentGroups as $group) {
            // Demand: Total SKS kelompok ini
            $totalLoad = $group->activities_sum_duration ?? 0;
            if ($totalLoad === 0) {
                continue;
            }

            // Supply: Total slot dikurangi batasan milik kelompok ini saja
            $totalUnavailable = $group->time_constraints_count;
            $totalAvailable = $totalPossibleSlots - $totalUnavailable;

            // Bandingkan
            if ($totalLoad > $totalAvailable) {
                $this->addIssue(
                    'Error',
                    "Beban SKS Kelompok '{$group->nama_kelompok}' (Prodi: {$group->prodi?->nama_prodi}) tidak dapat dipenuhi.",
                    "Total beban {$totalLoad} SKS, tetapi kelompok ini hanya memiliki {$totalAvailable} slot waktu luang."
                );
            }
        }
    }
}
