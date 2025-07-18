<?php

namespace App\Console\Commands;

use App\Events\ScheduleDataUpdatedEvent;
use App\Models\Activity;
use App\Models\Day;
use App\Models\MasterRuangan;
use App\Models\Schedule;
use App\Models\TimeSlot;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ParseFet extends Command
{
    protected $signature = 'fet:parse {file} {--no-cleanup : Jangan hapus data jadwal lama sebelum parsing}';

    protected $description = 'Parse a FET result file and import the generated timetable based on constraint lists';

    public function handle(): void
    {
        $filePath = $this->argument('file');
        Log::info("ParseFet: Memulai proses parse untuk file hasil: {$filePath}");

        if (! file_exists($filePath)) {
            Log::error("ParseFet: File tidak ditemukan di path: {$filePath}");
            $this->error('File tidak ditemukan.');

            return;
        }

        try {
            $xml = simplexml_load_file($filePath);
            if ($xml === false) {
                Log::error("ParseFet: Gagal membaca atau mem-parsing file XML: {$filePath}.");
                $this->error('Gagal membaca file XML. Pastikan formatnya benar.');

                return;
            }

            DB::transaction(function () use ($xml) {

                if (! $this->option('no-cleanup')) {
                    $this->cleanupOldSchedule();
                }

                // PERUBAHAN: Ubah cara memetakan timeSlots agar lebih mudah dicari secara berurutan
                $daysMap = Day::all()->keyBy('name');
                $timeSlotsCollection = TimeSlot::orderBy('start_time')->get();
                $roomsMap = MasterRuangan::all()->keyBy('nama_ruangan');
                $activitiesMap = Activity::with('teachers', 'subject', 'studentGroups')->get()->keyBy('id');

                $timePlacements = $this->buildTimePlacements($xml);
                $roomPlacements = $this->buildRoomPlacements($xml);

                $this->info(count($timePlacements).' penempatan jadwal ditemukan. Memulai proses impor...');

                $importedCount = 0;
                $skippedCount = 0;

                // --- PERULANGAN UTAMA DENGAN LOGIKA BARU ---
                foreach ($xml->Activities_List->Activity ?? [] as $activityXml) {
                    $activityId = (int) $activityXml->Id;
                    $timeData = $timePlacements[$activityId] ?? null;
                    $roomName = $roomPlacements[$activityId] ?? null;

                    if (!$timeData) continue;

                    $activity = $activitiesMap->get($activityId);
                    $day = $daysMap->get($timeData['day']);

                    // Cari indeks dari slot waktu mulai
                    $startingSlotIndex = $timeSlotsCollection->search(fn($slot) => date('H:i', strtotime($slot->start_time)) == $timeData['hour']);

                    if (!$roomName || !$roomsMap->has($roomName)) {
                        Log::warning("ParseFet: Ruangan '{$roomName}' untuk Activity ID {$activityId} tidak ditemukan.");
                        $skippedCount++;
                        continue;
                    }
                    $room = $roomsMap->get($roomName);

                    // Validasi awal
                    if ($activity && $day && $startingSlotIndex !== false && $room) {

                        // Ambil durasi SKS dari aktivitas (default 1 jika tidak ada)
                        $duration = $activity->duration ?? 1;

                        // Loop sebanyak durasi SKS untuk membuat jadwal berurutan
                        for ($i = 0; $i < $duration; $i++) {
                            // Ambil slot waktu yang benar berdasarkan indeks
                            $currentSlot = $timeSlotsCollection->get($startingSlotIndex + $i);

                            // Pastikan kita tidak keluar dari rentang slot waktu yang ada
                            if (!$currentSlot) {
                                Log::warning("ParseFet: Durasi untuk Activity ID {$activityId} melebihi slot waktu yang tersedia.");
                                break; // Hentikan loop jika slot waktu habis
                            }

                            $schedule = Schedule::create([
                                'activity_id'  => $activity->id,
                                'room_id'      => $room->id,
                                'time_slot_id' => $currentSlot->id, // Gunakan ID slot waktu saat ini
                                'day_id'       => $day->id,
                            ]);

                            if ($schedule && $activity->teachers->isNotEmpty()) {
                                $schedule->teachers()->sync($activity->teachers->pluck('id'));
                            }
                        }
                        $importedCount++;
                    } else {
                        Log::warning("ParseFet: Melewatkan jadwal untuk Activity ID {$activityId} karena data awal tidak lengkap.");
                        $skippedCount++;
                    }
                }

                $this->info("✅ Berhasil memproses {$importedCount} aktivitas jadwal.");
                if ($skippedCount > 0) {
                    $this->warn("{$skippedCount} aktivitas jadwal dilewati.");
                }
            });

            event(new ScheduleDataUpdatedEvent);

        } catch (Exception $e) {
            $this->error('Terjadi kesalahan saat parsing: '.$e->getMessage());
            Log::error('ParseFet Error: '.$e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
        }
    }

    protected function cleanupOldSchedule(): void
    {
        $this->info('Menghapus data jadwal lama...');
        DB::table('schedule_teacher')->delete();
        Schedule::query()->delete();
        $this->info('Data jadwal lama berhasil dihapus.');
    }

    /**
     * Membuat kamus [activity_id => ['day' => ..., 'hour' => ...]] dari constraint waktu.
     */
    private function buildTimePlacements(\SimpleXMLElement $xml): array
    {
        $placements = [];
        foreach ($xml->Time_Constraints_List->ConstraintActivityPreferredStartingTime ?? [] as $c) {
            $placements[(int) $c->Activity_Id] = ['day' => (string) $c->Day, 'hour' => (string) $c->Hour];
        }

        return $placements;
    }

    /**
     * Membuat kamus [activity_id => room_name] dari constraint ruang.
     */
    private function buildRoomPlacements(\SimpleXMLElement $xml): array
    {
        $placements = [];
        foreach ($xml->Space_Constraints_List->ConstraintActivityPreferredRoom ?? [] as $c) {
            $placements[(int) $c->Activity_Id] = (string) $c->Room;
        }

        return $placements;
    }
}
