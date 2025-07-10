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

        if (!file_exists($filePath)) {
            Log::error("ParseFet: File tidak ditemukan di path: {$filePath}");
            $this->error("File tidak ditemukan.");
            return;
        }

        try {
            $xml = simplexml_load_file($filePath);
            if ($xml === false) {
                Log::error("ParseFet: Gagal membaca atau mem-parsing file XML: {$filePath}.");
                $this->error("Gagal membaca file XML. Pastikan formatnya benar.");
                return;
            }

            DB::transaction(function () use ($xml) {
                // 1. Persiapan: Hapus data lama dan pramuat data master
                if (!$this->option('no-cleanup')) {
                    $this->cleanupOldSchedule();
                }
                $daysMap = Day::all()->keyBy('name');
                $timeSlotsMap = TimeSlot::all()->keyBy(fn($slot) => date('H:i', strtotime($slot->start_time)));
                $roomsMap = MasterRuangan::all()->keyBy('nama_ruangan');
                $activitiesMap = Activity::with('teachers', 'subject', 'studentGroup')->get()->keyBy('id');

                // 2. PERUBAHAN UTAMA: Buat Peta Penempatan dari Constraint Lists
                $timePlacements = $this->buildTimePlacements($xml);
                $roomPlacements = $this->buildRoomPlacements($xml);

                $this->info(count($timePlacements) . ' penempatan jadwal ditemukan. Memulai proses impor...');

                $importedCount = 0;
                $skippedCount = 0;

                // 3. Loop utama melalui SEMUA aktivitas yang ada di file
                foreach ($xml->Activities_List->Activity ?? [] as $activityXml) {
                    $activityId = (int)$activityXml->Id;

                    // Cari penempatan untuk aktivitas ini di peta yang sudah dibuat
                    $timeData = $timePlacements[$activityId] ?? null;
                    $roomName = $roomPlacements[$activityId] ?? null;

                    // Jika aktivitas ini tidak memiliki penempatan waktu, lewati
                    if (!$timeData) {
                        continue;
                    }

                    // Cocokkan semua data yang dibutuhkan
                    $activity = $activitiesMap->get($activityId);
                    $day = $daysMap->get($timeData['day']);
                    $timeSlot = $timeSlotsMap->get($timeData['hour']);

                    // Jika ruangan tidak ditemukan atau tidak ada, bisa lewati atau pakai default
                    if (!$roomName || !$roomsMap->has($roomName)) {
                        Log::warning("ParseFet: Ruangan '{$roomName}' untuk Activity ID {$activityId} tidak ditemukan. Jadwal dilewati.");
                        $skippedCount++;
                        continue;
                    }
                    $room = $roomsMap->get($roomName);

                    // Validasi akhir sebelum menyimpan
                    if ($activity && $day && $timeSlot && $room) {
                        $schedule = Schedule::create([
                            'activity_id'      => $activity->id,
                            'room_id'          => $room->id,
                            'time_slot_id'     => $timeSlot->id,
                            'day_id'           => $day->id,
                        ]);

                        if ($schedule && $activity->teachers->isNotEmpty()) {
                            $schedule->teachers()->sync($activity->teachers->pluck('id'));
                        }
                        $importedCount++;
                    } else {
                        Log::warning("ParseFet: Melewatkan jadwal untuk Activity ID {$activityId} karena data tidak lengkap.", [
                            'activity_found' => !!$activity, 'day_found' => !!$day, 'timeslot_found' => !!$timeSlot, 'room_found' => !!$room
                        ]);
                        $skippedCount++;
                    }
                }

                $this->info("✅ Berhasil mengimpor {$importedCount} jadwal.");
                if ($skippedCount > 0) {
                    $this->warn("{$skippedCount} jadwal dilewati karena data tidak lengkap atau ruangan tidak ditemukan.");
                }
            });

            event(new ScheduleDataUpdatedEvent());

        } catch (Exception $e) {
            $this->error('Terjadi kesalahan saat parsing: ' . $e->getMessage());
            Log::error("ParseFet Error: " . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
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
        foreach($xml->Time_Constraints_List->ConstraintActivityPreferredStartingTime ?? [] as $c) {
            $placements[(int)$c->Activity_Id] = ['day' => (string)$c->Day, 'hour' => (string)$c->Hour];
        }
        return $placements;
    }

    /**
     * Membuat kamus [activity_id => room_name] dari constraint ruang.
     * ASUMSI: Struktur constraint ruang mirip dengan constraint waktu.
     */
    private function buildRoomPlacements(\SimpleXMLElement $xml): array
    {
        $placements = [];
        // Perhatikan nama constraint ini mungkin berbeda di file Anda. Sesuaikan jika perlu.
        foreach($xml->Space_Constraints_List->ConstraintActivityPreferredRoom ?? [] as $c) {
            $placements[(int)$c->Activity_Id] = (string)$c->Room;
        }
        return $placements;
    }
}
