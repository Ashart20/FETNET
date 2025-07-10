<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\ActivityTag;
use App\Models\Building;
use App\Models\Day;
use App\Models\MasterRuangan;
use App\Models\Prodi;
use App\Models\RoomTimeConstraint;
use App\Models\StudentGroup;
use App\Models\StudentGroupTimeConstraint;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherTimeConstraint;
use App\Models\TimeSlot;
use DOMDocument;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class FetFileGeneratorService
{
    private const DEFAULT_WEIGHT = '100';

    /**
     * Menghasilkan file .fet untuk program studi yang diberikan sesuai struktur baru.
     *
     * @param Prodi $prodi
     * @param string|null $customFilePath
     * @return string Path file .fet yang dihasilkan.
     */
    public function generateForProdi(Prodi $prodi, ?string $customFilePath = null): string
    {
        // === Langkah 1: Ambil semua data yang diperlukan dari database ===
        $data = $this->fetchDataForProdi($prodi);

        // === Langkah 2: Buat struktur dasar XML (file .fet) ===
        $xml = new SimpleXMLElement('<fet version="7.2.5"></fet>');
        // Penyesuaian: Menambahkan tag <Mode>
        $xml->addChild('Mode', 'Official');
        $xml->addChild('Institution_Name', 'FPTK UPI - ' . htmlspecialchars($prodi->nama_prodi));
        $xml->addChild('Comments', 'Dibuat secara otomatis oleh sistem penjadwalan pada ' . now());

        // === Langkah 3: Isi XML dengan data yang sudah diambil ===
        $this->addDeclarations($xml, $data);
        $this->addConstraints($xml, $data);

        // Menambahkan node kosong di akhir jika diperlukan
        $xml->addChild('Timetable_Generation_Options_List');

        // === Langkah 4: Simpan file XML yang sudah diformat ===
        return $this->saveXmlToFile($xml, $prodi, $customFilePath);
    }

    /**
     * Mengambil data dari database.
     */
    // app/Services/FetFileGeneratorService.php
    private function fetchDataForProdi(Prodi $prodi): array
    {
        $prodiId = $prodi->id;

        // [1] Logika pengambilan dosen berdasarkan cluster
        $teachersQuery = Teacher::query();
        if ($prodi->cluster_id) {
            $prodiIdsInCluster = Prodi::where('cluster_id', $prodi->cluster_id)->pluck('id');
            $teachersQuery->whereHas('prodis', function ($query) use ($prodiIdsInCluster) {
                $query->whereIn('prodis.id', $prodiIdsInCluster);
            });
        } else {
            // Fallback jika tidak ada cluster, ambil dosen dari prodi saat ini saja
            $teachersQuery->whereHas('prodis', function ($query) use ($prodiId) {
                $query->where('prodis.id', $prodiId);
            });
        }

        // [2] Ambil grup mahasiswa dari prodi saat ini saja
        $studentGroups = StudentGroup::where('prodi_id', $prodiId)
            ->with('childrenRecursive')
            ->whereNull('parent_id')
            ->get();

        return [
            'teachers' => $teachersQuery->distinct()->get(), // Ambil data dosen yang sudah difilter
            'subjects' => Subject::where('prodi_id', $prodiId)->get(),
            'activities' => Activity::where('prodi_id', $prodiId)
                ->with(['teachers', 'subject', 'studentGroup', 'activityTag', 'preferredRooms'])
                ->get(),
            'rooms' => MasterRuangan::with('building')->get(),
            'buildings' => Building::all(),
            'days' => Day::orderBy('id', 'asc')->get(),
            'timeSlots' => TimeSlot::orderBy('start_time')->get(),
            'teacherConstraints' => TeacherTimeConstraint::whereHas('teacher', function ($q) use ($teachersQuery) {
                $q->whereIn('id', $teachersQuery->pluck('id')); // Ambil batasan dari semua dosen di cluster
            })->with(['teacher', 'day', 'timeSlot'])->get(),
            'roomConstraints' => RoomTimeConstraint::with(['masterRuangan', 'day', 'timeSlot'])->get(),
            'studentGroupConstraints' => StudentGroupTimeConstraint::whereHas('studentGroup', fn($q) => $q->where('prodi_id', $prodiId))->with(['studentGroup', 'day', 'timeSlot'])->get(),
            'activityTags' => ActivityTag::all(),
            'studentGroups' => $studentGroups,
        ];
    }

    /**
     * Menambahkan semua node deklarasi (Dosen, Matkul, Ruangan, dll.) ke XML.
     */
    private function addDeclarations(SimpleXMLElement $xml, array $data): void
    {
        $this->addDaysList($xml, $data['days']);
        $this->addHoursList($xml, $data['timeSlots']);
        $this->addSubjectsList($xml, $data['subjects']);
        $this->addActivityTagsList($xml, $data['activityTags']);
        $this->addTeachersList($xml, $data['teachers']);
        $this->addStudentsList($xml, $data['studentGroups']); // Menggunakan data hierarkis
        $this->addActivitiesList($xml, $data['activities']);
        $this->addBuildingsList($xml, $data['buildings']);
        $this->addRoomsList($xml, $data['rooms']);
    }

    /**
     * Menambahkan semua node batasan (waktu dan ruang) ke XML.
     */
    private function addConstraints(SimpleXMLElement $xml, array $data): void
    {
        $this->addTimeConstraints($xml, $data);
        $this->addSpaceConstraints($xml, $data);
    }

    // --- METODE PEMBUATAN DEKLARASI (DISESUAIKAN) ---

    private function addDaysList(SimpleXMLElement $xml, Collection $days): void
    {
        $list = $xml->addChild('Days_List');
        $list->addChild('Number_of_Days', $days->count());
        foreach ($days as $day) {
            $dayNode = $list->addChild('Day');
            $dayNode->addChild('Name', htmlspecialchars($day->name));
            $dayNode->addChild('Long_Name', ''); // Penyesuaian
        }
    }

    private function addHoursList(SimpleXMLElement $xml, Collection $timeSlots): void
    {
        $list = $xml->addChild('Hours_List');
        $list->addChild('Number_of_Hours', $timeSlots->count());
        foreach ($timeSlots as $slot) {
            $hourNode = $list->addChild('Hour');
            $hourNode->addChild('Name', date('H:i', strtotime($slot->start_time)));
            $hourNode->addChild('Long_Name', ''); // Penyesuaian
        }
    }

    private function addSubjectsList(SimpleXMLElement $xml, Collection $subjects): void
    {
        $list = $xml->addChild('Subjects_List');
        foreach ($subjects as $subject) {
            $node = $list->addChild('Subject');
            // Penyesuaian: <Name> menggunakan nama matkul, sesuai contoh.
            $node->addChild('Name', htmlspecialchars($subject->nama_matkul));
            $node->addChild('Long_Name', '');
            $node->addChild('Code', '');
            $node->addChild('Comments', htmlspecialchars($subject->comments ?? ''));
        }
    }

    private function addActivityTagsList(SimpleXMLElement $xml, Collection $tags): void
    {
        $list = $xml->addChild('Activity_Tags_List');
        foreach ($tags as $tag) {
            $node = $list->addChild('Activity_Tag');
            $node->addChild('Name', htmlspecialchars($tag->name));
            // Penyesuaian
            $node->addChild('Long_Name', '');
            $node->addChild('Code', '');
            $node->addChild('Printable', 'true');
            $node->addChild('Comments', '');
        }
    }

    private function addTeachersList(SimpleXMLElement $xml, Collection $teachers): void
    {
        $list = $xml->addChild('Teachers_List');
        foreach ($teachers as $teacher) {
            $node = $list->addChild('Teacher');
            // Identifier tetap KODE DOSEN karena unik
            $node->addChild('Name', htmlspecialchars($teacher->kode_dosen));
            // Penyesuaian: Tambah tag-tag kosong sesuai struktur
            $node->addChild('Long_Name', htmlspecialchars($teacher->nama_dosen));
            $node->addChild('Code', '');
            $node->addChild('Target_Number_of_Hours', '0');
            $node->addChild('Qualified_Subjects');
            $node->addChild('Comments', '');
        }
    }

    /**
     * PERUBAHAN BESAR: Membuat struktur Students_List secara hierarkis/rekursif.
     * Asumsi: Model StudentGroup memiliki relasi parent-child.
     * - `parent_id` di tabel `student_groups`
     * - Relasi di model: `public function childrenRecursive() { return $this->hasMany(StudentGroup::class, 'parent_id')->with('childrenRecursive'); }`
     */
    private function addStudentsList(SimpleXMLElement $xml, Collection $years): void
    {
        $list = $xml->addChild('Students_List');
        foreach ($years as $year) {
            $yearNode = $list->addChild('Year');
            $yearNode->addChild('Name', htmlspecialchars($year->nama_kelompok));
            $yearNode->addChild('Long_Name', '');
            $yearNode->addChild('Code', '');
            $yearNode->addChild('Number_of_Students', $year->jumlah_mahasiswa ?? 0);
            $yearNode->addChild('Comments', '');

            if ($year->childrenRecursive->isNotEmpty()) {
                foreach ($year->childrenRecursive as $group) {
                    $this->addStudentGroupRecursive($yearNode, $group);
                }
            }
        }
    }

    private function addStudentGroupRecursive(SimpleXMLElement $parentNode, StudentGroup $group): void
    {
        $groupNode = $parentNode->addChild('Group');
        $groupNode->addChild('Name', htmlspecialchars($group->nama_kelompok));
        $groupNode->addChild('Long_Name', '');
        $groupNode->addChild('Code', '');
        $groupNode->addChild('Number_of_Students', $group->jumlah_mahasiswa ?? 0);
        $groupNode->addChild('Comments', '');

        if ($group->childrenRecursive->isNotEmpty()) {
            foreach ($group->childrenRecursive as $subgroup) {
                // Di FET, level setelah Group adalah Subgroup.
                $subgroupNode = $groupNode->addChild('Subgroup');
                $subgroupNode->addChild('Name', htmlspecialchars($subgroup->nama_kelompok));
                $subgroupNode->addChild('Long_Name', '');
                $subgroupNode->addChild('Code', '');
                $subgroupNode->addChild('Number_of_Students', $subgroup->jumlah_mahasiswa ?? 0);
                $subgroupNode->addChild('Comments', '');
            }
        }
    }


    private function addActivitiesList(SimpleXMLElement $xml, Collection $activities): void
    {
        $activitiesList = $xml->addChild('Activities_List');
        foreach ($activities as $activity) {
            if ($activity->teachers->isEmpty() || !$activity->subject || !$activity->studentGroup) {
                Log::warning("Melewati Aktivitas ID: {$activity->id} karena data Dosen/Matkul/Kelompok tidak lengkap.");
                continue;
            }

            $activityNode = $activitiesList->addChild('Activity');

            // Referensi Dosen tetap menggunakan KODE DOSEN
            foreach($activity->teachers as $teacher) {
                $activityNode->addChild('Teacher', htmlspecialchars($teacher->kode_dosen));
            }
            // Penyesuaian: Referensi Matkul menggunakan NAMA MATKUL
            $activityNode->addChild('Subject', htmlspecialchars($activity->subject->nama_matkul));
            $activityNode->addChild('Students', htmlspecialchars($activity->studentGroup->nama_kelompok));

            $activityNode->addChild('Duration', $activity->duration);
            $activityNode->addChild('Total_Duration', $activity->duration);
            $activityNode->addChild('Id', $activity->id);
            $activityNode->addChild('Activity_Group_Id', 0);
            $activityNode->addChild('Active', 'true');
            $activityNode->addChild('Comments', '');

            if ($activity->activityTag) {
                $activityNode->addChild('Activity_Tag', htmlspecialchars($activity->activityTag->name));
            }
        }
    }

    private function addBuildingsList(SimpleXMLElement $xml, Collection $buildings): void
    {
        $list = $xml->addChild('Buildings_List');
        foreach ($buildings as $building) {
            $node = $list->addChild('Building');
            $node->addChild('Name', htmlspecialchars($building->code)); // Menggunakan kode gedung
            // Penyesuaian
            $node->addChild('Long_Name', '');
            $node->addChild('Code', '');
            $node->addChild('Comments', '');
        }
    }

    private function addRoomsList(SimpleXMLElement $xml, Collection $rooms): void
    {
        $list = $xml->addChild('Rooms_List');
        foreach ($rooms as $room) {
            $node = $list->addChild('Room');
            $node->addChild('Name', htmlspecialchars($room->nama_ruangan));
            // Penyesuaian
            $node->addChild('Long_Name', '');
            $node->addChild('Code', '');
            $node->addChild('Building', htmlspecialchars($room->building->code ?? ''));
            $node->addChild('Capacity', $room->kapasitas);
            $node->addChild('Virtual', 'false');
            $node->addChild('Comments', '');
        }
    }

    // --- METODE CONSTRAINTS (PENYESUAIAN IDENTIFIER) ---

    private function addTimeConstraints(SimpleXMLElement $xml, array $data): void
    {
        $list = $xml->addChild('Time_Constraints_List');
        $list->addChild('ConstraintBasicCompulsoryTime')->addChild('Weight_Percentage', self::DEFAULT_WEIGHT);

        $this->addTeacherNotAvailableTimes($list, $data['teacherConstraints']);
        $this->addStudentNotAvailableTimes($list, $data['studentGroupConstraints']);
        $this->addTeacherMaxHoursDaily($list, $data['teachers']);
        $this->addStudentsMaxHoursDaily($list, $data['studentGroups']);
    }

    private function addSpaceConstraints(SimpleXMLElement $xml, array $data): void
    {
        $list = $xml->addChild('Space_Constraints_List');
        $list->addChild('ConstraintBasicCompulsorySpace')->addChild('Weight_Percentage', self::DEFAULT_WEIGHT);

        $this->addRoomNotAvailableTimes($list, $data['roomConstraints']);
        $this->addActivityPreferredRooms($list, $data['activities'], $data['rooms']);
    }

    private function addTeacherNotAvailableTimes(SimpleXMLElement $timeList, Collection $constraints): void
    {
        foreach ($constraints->groupBy('teacher_id') as $items) {
            $first = $items->first();
            if (!$first || !$first->teacher) continue;

            $cNode = $timeList->addChild('ConstraintTeacherNotAvailableTimes');
            $cNode->addChild('Weight_Percentage', self::DEFAULT_WEIGHT);
            $cNode->addChild('Teacher', htmlspecialchars($first->teacher->kode_dosen)); // Identifier: KODE DOSEN
            $cNode->addChild('Number_of_Not_Available_Times', $items->count());
            foreach ($items as $item) {
                $notAvailableNode = $cNode->addChild('Not_Available_Time');
                $notAvailableNode->addChild('Day', $item->day->name);
                $notAvailableNode->addChild('Hour', date('H:i', strtotime($item->timeSlot->start_time)));
            }
        }
    }


    private function addStudentNotAvailableTimes(SimpleXMLElement $timeList, Collection $constraints): void
    {
        foreach ($constraints->groupBy('student_group_id') as $items) {
            $first = $items->first();
            if (!$first || !$first->studentGroup) continue;

            $cNode = $timeList->addChild('ConstraintStudentsSetNotAvailableTimes');
            $cNode->addChild('Weight_Percentage', self::DEFAULT_WEIGHT);
            $cNode->addChild('Students', htmlspecialchars($first->studentGroup->nama_kelompok));
            $cNode->addChild('Number_of_Not_Available_Times', $items->count());
            foreach ($items as $item) {
                $notAvailableNode = $cNode->addChild('Not_Available_Time');
                $notAvailableNode->addChild('Day', $item->day->name);
                $notAvailableNode->addChild('Hour', date('H:i', strtotime($item->timeSlot->start_time)));
            }
        }
    }

    private function addTeacherMaxHoursDaily(SimpleXMLElement $timeList, Collection $teachers): void
    {
        foreach ($teachers as $teacher) {
            $cNode = $timeList->addChild('ConstraintTeacherMaxHoursDaily');
            $cNode->addChild('Weight_Percentage', self::DEFAULT_WEIGHT);
            $cNode->addChild('Maximum_Hours_Daily', config('fet.max_hours_teacher', 8));
            $cNode->addChild('Teacher', htmlspecialchars($teacher->kode_dosen));
        }
    }

    private function addStudentsMaxHoursDaily(SimpleXMLElement $timeList, Collection $studentGroups): void
    {
        foreach ($studentGroups as $group) {
            $cNode = $timeList->addChild('ConstraintStudentsSetMaxHoursDaily');
            $cNode->addChild('Weight_Percentage', self::DEFAULT_WEIGHT);
            $cNode->addChild('Maximum_Hours_Daily', config('fet.max_hours_student', 10));
            $cNode->addChild('Students', htmlspecialchars($group->nama_kelompok));
        }
    }

    private function addRoomNotAvailableTimes(SimpleXMLElement $spaceList, Collection $constraints): void
    {
        foreach ($constraints->groupBy('master_ruangan_id') as $items) {
            $first = $items->first();
            if (!$first || !$first->masterRuangan) continue;

            $cNode = $spaceList->addChild('ConstraintRoomNotAvailableTimes');
            $cNode->addChild('Weight_Percentage', self::DEFAULT_WEIGHT);
            $cNode->addChild('Room', htmlspecialchars($first->masterRuangan->nama_ruangan));
            $cNode->addChild('Number_of_Not_Available_Times', $items->count());
            foreach ($items as $item) {
                $notAvailableNode = $cNode->addChild('Not_Available_Time');
                $notAvailableNode->addChild('Day', $item->day->name);
                $notAvailableNode->addChild('Hour', date('H:i', strtotime($item->timeSlot->start_time)));
            }
        }
    }

    private function addActivityPreferredRooms(SimpleXMLElement $spaceList, Collection $activities, Collection $allRooms): void
    {
        // 1. Pramuat semua tipe ruangan yang kita butuhkan untuk efisiensi
        $labRooms = $allRooms->where('tipe', 'LABORATORIUM');
        $theoryRooms = $allRooms->where('tipe', 'KELAS_TEORI');

        foreach ($activities as $activity) {
            // Ambil preferensi yang diinput manual oleh user
            $preferredRooms = $activity->preferredRooms;

            if ($preferredRooms->isEmpty() && isset($activity->activityTag)) {
                $tagName = $activity->activityTag->name;

                if ($tagName === 'PRAKTIKUM') {
                    // Jika tag-nya PRAKTIKUM, set preferensi ke semua ruangan Lab
                    $preferredRooms = $labRooms;
                } elseif ($tagName === 'GANJIL' || $tagName === 'GENAP') {
                    // Jika tag-nya GANJIL atau GENAP, set preferensi ke semua ruangan Teori
                    $preferredRooms = $theoryRooms;
                }
            }

            // Jika setelah semua pengecekan, tetap tidak ada preferensi, lewati aktivitas ini
            if ($preferredRooms->isEmpty()) {
                continue;
            }

            // 3. Buat blok XML Constraint seperti biasa menggunakan data $preferredRooms
            $cNode = $spaceList->addChild('ConstraintActivityPreferredRooms');
            $cNode->addChild('Weight_Percentage', '95');
            $cNode->addChild('Activity_Id', $activity->id);
            $cNode->addChild('Number_of_Preferred_Rooms', $preferredRooms->count());
            foreach ($preferredRooms as $room) {
                $cNode->addChild('Preferred_Room', htmlspecialchars($room->nama_ruangan));
            }
        }
    }

    private function saveXmlToFile(SimpleXMLElement $xml, Prodi $prodi, ?string $customFilePath): string
    {
        if (is_null($customFilePath)) {
            $dirPath = storage_path('app/fet-generator/inputs');
            if (!file_exists($dirPath)) {
                mkdir($dirPath, 0775, true);
            }
            $fileName = 'input_prodi_' . $prodi->kode . '_' . time() . '.fet';
            $customFilePath = $dirPath . '/' . $fileName;
        }

        $dom = new DOMDocument('1.0', 'UTF-8'); // Set encoding
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        $dom->save($customFilePath);

        return $customFilePath;
    }
}
