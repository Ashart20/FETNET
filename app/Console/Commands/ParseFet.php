<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\TimeSlot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ParseFet extends Command
{
    protected $signature = 'fet:parse {file}';
    protected $description = 'Parse a .fet file and insert data into schedules table';

    public function handle(): void
    {
        $file = $this->argument('file');
        // HAPUS DATA LAMA
        Schedule::query()->delete();
        TimeSlot::query()->delete();
        Room::query()->delete();
        if (!file_exists($file)) {
            $this->error("❌ File tidak ditemukan: $file");
            return;
        }

        $xml = simplexml_load_file($file);
        if (!$xml) {
            $this->error("❌ Gagal membaca file XML.");
            return;
        }

        $subjectMap = [];
        foreach ($xml->Subjects_List->Subject ?? [] as $subject) {
            $subjectMap[(string)$subject->Name] = (string)$subject->Code;
        }

        $teacherMap = [];
        foreach ($xml->Teachers_List->Teacher ?? [] as $teacher) {
            $teacherMap[(string)$teacher->Name] = (string)$teacher->Code;
        }

        $activityMap = [];
        foreach ($xml->Activities_List->Activity ?? [] as $activity) {
            $id = (int)$activity->Id;
            $activityMap[$id] = [
                'subject' => (string)$activity->Subject,
                'teacher' => (string)$activity->Teacher,
                'students' => (string)$activity->Students,
                'duration' => (int)$activity->Duration,
            ];
        }

        $constraints = [];
        foreach ($xml->Time_Constraints_List->ConstraintActivityPreferredStartingTime ?? [] as $constraint) {
            if (!isset($constraint->Activity_Id, $constraint->Day, $constraint->Hour)) {
                continue;
            }

            $id = (int)$constraint->Activity_Id;
            $constraints[$id] = [
                'day' => (string)$constraint->Day,
                'hour' => (string)$constraint->Hour,
            ];
        }

        $dayMap = [
            'D1' => 'Senin',
            'D2' => 'Selasa',
            'D3' => 'Rabu',
            'D4' => 'Kamis',
            'D5' => 'Jumat',
            'D6' => 'Sabtu',
            'D7' => 'Minggu',
        ];

        $hourMap = [
            'H1' => '07:00', 'H2' => '07:45', 'H3' => '08:30', 'H4' => '09:15',
            'H5' => '10:00', 'H6' => '10:45', 'H7' => '11:30', 'H8' => '12:15',
            'H9' => '13:00', 'H10' => '13:45', 'H11' => '14:30', 'H12' => '15:15',
            'H13' => '16:00',
        ];
// Ambil kode MK dari Subjects
        $subjectMap = [];
        foreach ($xml->Subjects_List->Subject ?? [] as $subject) {
            $subjectMap[(string)$subject->Name] = (string)$subject->Code;
        }

// Ambil kode dosen dari Teachers
        $teacherMap = [];
        foreach ($xml->Teachers_List->Teacher ?? [] as $teacher) {
            $teacherMap[(string)$teacher->Name] = (string)$teacher->Code;
        }
        if (isset($xml->Rooms_List->Room)) {
            $roomCount = 0;
            foreach ($xml->Rooms_List->Room as $room) {
                Room::updateOrCreate([
                    'name' => (string) $room->Name,
                ], [
                    'capacity' => (int) $room->Capacity ?: null,
                ]);
                $roomCount++;
            }
            $this->info("📦 Jumlah room ditemukan: {$roomCount}");
        } else {
            Room::firstOrCreate(['name' => 'TBD'], ['capacity' => null]);
            $this->warn("⚠️ Tidak ada Room ditemukan, menambahkan default 'TBD'");
        }


        $count = 0;
        foreach ($constraints as $id => $constraint) {
            $detail = $activityMap[$id] ?? null;
            if (!$detail) continue;
            $roomName = (string) ($xml->Space_Constraints_List->xpath("ConstraintActivityPreferredRoom[Activity_Id='$id']/Room") [0] ?? 'TBD');

            $room = Room::where('name', $roomName)->first() ?? Room::where('name', 'TBD')->first();
            $rawDay = $constraint['day'] ?? null;
            $day = $dayMap[$rawDay] ?? null;

            if (!$day) {
                $this->warn("⚠️ Day tidak dikenali untuk activity ID $id: " . json_encode($constraint));
                continue;
            }
            $jam = $hourMap[$constraint['hour']] ?? '00:00';

            $start = Carbon::createFromFormat('H:i', $hourMap[$constraint['hour']] ?? '00:00');
            $duration = $detail['duration'] * 45;
            $end = $start->copy()->addMinutes($duration);

            $slot = TimeSlot::firstOrCreate(
                [
                    'day' => $day,
                    'start_time' => $start->format('H:i'),
                    'end_time' => $end->format('H:i'),
                ]
            );
            $this->info("⏰ Jumlah time slot tercatat: " . TimeSlot::count());

            Schedule::create([
                'activity_id' => $id,
                'kode_mk' => $subjectMap[$detail['subject']] ?? null,
                'subject' => $detail['subject'],
                'kode_dosen' => $teacherMap[$detail['teacher']] ?? null,
                'teacher' => $detail['teacher'],
                'kelas' => $detail['students'],
                'sks' => $detail['duration'],
                'room_id' => $room?->id,
                'time_slot_id' => $slot->id,

            ]);
            $count++;
        }

        $this->info("✅ Inserted $count schedules.");
    }
}
