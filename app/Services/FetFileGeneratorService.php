<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\ActivityTag;
use App\Models\Building;
use App\Models\Day;
use App\Models\FetFile;
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
     * Menghasilkan satu file .fet untuk SELURUH FAKULTAS.
     *
     * @return string Path file .fet yang dihasilkan.
     */
    public function generateForFaculty(?string $customFilePath = null, ?int $userId = null): string // <-- Terima $userId
    {
        Log::info('Memulai pengambilan data untuk seluruh fakultas.');
        $data = $this->fetchDataForFaculty();
        Log::info('Pengambilan data selesai. Ditemukan '.$data['activities']->count().' aktivitas.');

        $xml = new SimpleXMLElement('<fet version="7.2.5"></fet>');
        $xml->addChild('Mode', 'Official');
        $xml->addChild('Institution_Name', 'Fakultas Pendidikan Teknologi dan Kejuruan UPI'); // Nama institusi diubah menjadi level fakultas
        $xml->addChild('Comments', 'Dibuat secara otomatis oleh sistem penjadwalan pada '.now());

        $this->addDeclarations($xml, $data);
        $this->addConstraints($xml, $data);

        $xml->addChild('Timetable_Generation_Options_List');

        return $this->saveXmlToFile($xml, null, $customFilePath, $userId); // null karena tidak lagi spesifik prodi
    }

    /**
     * Mengambil semua data yang relevan untuk seluruh fakultas dari database.
     */
    private function fetchDataForFaculty(): array
    {
        // Mengambil semua data tanpa filter prodi
        return [
            'teachers' => Teacher::with('prodis')->distinct()->get(),
            'subjects' => Subject::with('prodi')->get(),
            'activities' => Activity::with(['teachers', 'subject.prodi', 'studentGroups.prodi', 'activityTag', 'preferredRooms'])->get(),
            'rooms' => MasterRuangan::with('building')->get(),
            'buildings' => Building::all(),
            'days' => Day::orderBy('id', 'asc')->get(),
            'timeSlots' => TimeSlot::orderBy('start_time')->get(),
            'teacherConstraints' => TeacherTimeConstraint::with(['teacher', 'day', 'timeSlot'])->get(),
            'roomConstraints' => RoomTimeConstraint::with(['masterRuangan', 'day', 'timeSlot'])->get(),
            'studentGroupConstraints' => StudentGroupTimeConstraint::with(['studentGroup.prodi', 'day', 'timeSlot'])->get(),
            'activityTags' => ActivityTag::all(),
            'studentGroups' => StudentGroup::with('childrenRecursive.prodi', 'prodi')->whereNull('parent_id')->get(),
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
        $this->addStudentsList($xml, $data['studentGroups']);
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

    /**
     * Helper untuk mendapatkan nama unik kelompok mahasiswa dengan prefix kode prodi.
     */
    private function getUniqueStudentGroupName(StudentGroup $group): string
    {
        if ($group->prodi) {
            return $group->prodi->kode.'-'.$group->nama_kelompok;
        }

        // Fallback jika grup tidak punya prodi (seharusnya tidak terjadi)
        return $group->nama_kelompok;
    }

    /**
     * Helper untuk mendapatkan nama unik mata kuliah dengan prefix kode prodi.
     */
    private function getUniqueSubjectName(Subject $subject): string
    {
        if ($subject->prodi) {
            return $subject->prodi->kode.'-'.$subject->nama_matkul;
        }

        return $subject->nama_matkul;
    }

    private function addDaysList(SimpleXMLElement $xml, Collection $days): void
    {
        $list = $xml->addChild('Days_List');
        $list->addChild('Number_of_Days', $days->count());
        foreach ($days as $day) {
            $dayNode = $list->addChild('Day');
            $dayNode->addChild('Name', htmlspecialchars($day->name));
            $dayNode->addChild('Long_Name', '');
        }
    }

    private function addHoursList(SimpleXMLElement $xml, Collection $timeSlots): void
    {
        $list = $xml->addChild('Hours_List');
        $list->addChild('Number_of_Hours', $timeSlots->count());
        foreach ($timeSlots as $slot) {
            $hourNode = $list->addChild('Hour');
            $hourNode->addChild('Name', date('H:i', strtotime($slot->start_time)));
            $hourNode->addChild('Long_Name', '');
        }
    }

    private function addSubjectsList(SimpleXMLElement $xml, Collection $subjects): void
    {
        $list = $xml->addChild('Subjects_List');
        foreach ($subjects as $subject) {
            $node = $list->addChild('Subject');
            $node->addChild('Name', htmlspecialchars($this->getUniqueSubjectName($subject)));
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
            $node->addChild('Name', htmlspecialchars($teacher->kode_dosen));
            $node->addChild('Long_Name', htmlspecialchars($teacher->nama_dosen));
            $node->addChild('Code', htmlspecialchars($teacher->kode_univ));
            $node->addChild('Target_Number_of_Hours', '0');
            $node->addChild('Qualified_Subjects');
            $node->addChild('Comments', '');
        }
    }

    private function addStudentsList(SimpleXMLElement $xml, Collection $years): void
    {
        $list = $xml->addChild('Students_List');
        foreach ($years as $year) {
            $yearNode = $list->addChild('Year');
            // PENTING: Membuat nama kelompok unik
            $yearNode->addChild('Name', htmlspecialchars($this->getUniqueStudentGroupName($year)));
            $yearNode->addChild('Long_Name', '');
            $yearNode->addChild('Code', '');
            $yearNode->addChild('Number_of_Students', $year->jumlah_mahasiswa ?? 0);
            $yearNode->addChild('Comments', '');

            if ($year->children->isNotEmpty()) {
                foreach ($year->children as $group) {
                    $this->addStudentGroupRecursive($yearNode, $group);
                }
            }
        }
    }

    private function addStudentGroupRecursive(SimpleXMLElement $parentNode, StudentGroup $group): void
    {
        $groupNode = $parentNode->addChild('Group');

        $groupNode->addChild('Name', htmlspecialchars($this->getUniqueStudentGroupName($group)));
        $groupNode->addChild('Long_Name', '');
        $groupNode->addChild('Code', '');
        $groupNode->addChild('Number_of_Students', $group->jumlah_mahasiswa ?? 0);
        $groupNode->addChild('Comments', '');

        if ($group->children->isNotEmpty()) {
            foreach ($group->children as $subgroup) {

                $subgroupNode = $groupNode->addChild('Subgroup');
                $subgroupNode->addChild('Name', htmlspecialchars($this->getUniqueStudentGroupName($subgroup)));
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
            if ($activity->teachers->isEmpty() || ! $activity->subject || $activity->studentGroups->isEmpty()) {
                Log::warning("Melewati Aktivitas ID: {$activity->id} karena data Dosen/Matkul/Kelompok tidak lengkap.");

                continue;
            }

            $activityNode = $activitiesList->addChild('Activity');

            foreach ($activity->teachers as $teacher) {
                $activityNode->addChild('Teacher', htmlspecialchars($teacher->kode_dosen));
            }

            $activityNode->addChild('Subject', htmlspecialchars($this->getUniqueSubjectName($activity->subject)));

            foreach ($activity->studentGroups as $studentGroup) {

                $activityNode->addChild('Students', htmlspecialchars($this->getUniqueStudentGroupName($studentGroup)));
            }

            $activityNode->addChild('Duration', $activity->duration);
            $activityNode->addChild('Total_Duration', $activity->duration);
            $activityNode->addChild('Id', $activity->id);
            $activityNode->addChild('Activity_Group_Id', 0);
            $activityNode->addChild('Active', 'true');
            $activityNode->addChild('Comments', htmlspecialchars($activity->name ?? ''));

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
            $node->addChild('Long_Name', '');
            $node->addChild('Code', '');
            $node->addChild('Building', htmlspecialchars($room->building->code ?? ''));
            $node->addChild('Capacity', $room->kapasitas);
            $node->addChild('Virtual', 'false');
            $node->addChild('Comments', '');
        }
    }

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
            if (! $first || ! $first->teacher) {
                continue;
            }

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
            if (! $first || ! $first->studentGroup || ! $first->studentGroup->prodi) {
                continue;
            }

            $cNode = $timeList->addChild('ConstraintStudentsSetNotAvailableTimes');
            $cNode->addChild('Weight_Percentage', self::DEFAULT_WEIGHT);

            $cNode->addChild('Students', htmlspecialchars($this->getUniqueStudentGroupName($first->studentGroup)));
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
            $cNode->addChild('Maximum_Hours_Daily', config('fet.max_hours_teacher', 12));
            $cNode->addChild('Teacher', htmlspecialchars($teacher->kode_dosen));
        }
    }

    private function addStudentsMaxHoursDaily(SimpleXMLElement $timeList, Collection $studentGroups): void
    {
        foreach ($studentGroups as $group) {
            // Handle children recursively
            if ($group->childrenRecursive->isNotEmpty()) {
                $this->addStudentsMaxHoursDaily($timeList, $group->childrenRecursive);
            }

            $cNode = $timeList->addChild('ConstraintStudentsSetMaxHoursDaily');
            $cNode->addChild('Weight_Percentage', self::DEFAULT_WEIGHT);
            $cNode->addChild('Maximum_Hours_Daily', config('fet.max_hours_student', 10));
            // PENTING: Gunakan nama unik untuk referensi constraint
            $cNode->addChild('Students', htmlspecialchars($this->getUniqueStudentGroupName($group)));
        }
    }

    private function addRoomNotAvailableTimes(SimpleXMLElement $spaceList, Collection $constraints): void
    {
        foreach ($constraints->groupBy('master_ruangan_id') as $items) {
            $first = $items->first();
            if (! $first || ! $first->masterRuangan) {
                continue;
            }

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
        $labRooms = $allRooms->where('tipe', 'LABORATORIUM');
        $theoryRooms = $allRooms->where('tipe', 'KELAS_TEORI');

        foreach ($activities as $activity) {
            $preferredRooms = $activity->preferredRooms;

            // Jika tidak ada preferensi ruangan yang diatur manual
            if ($preferredRooms->isEmpty()) {
                if ($activity->activityTag) {
                    $tagName = $activity->activityTag->name;

                    if ($tagName === 'PRAKTIKUM') {
                        $preferredRooms = $labRooms;
                    }
                    // PERBAIKAN DI SINI: Gunakan 'KELAS TEORI' dengan spasi
                    elseif ($tagName === 'KELAS TEORI') {
                        $preferredRooms = $theoryRooms;
                    }
                    // Jika tag lain (misal: PILIHAN), gunakan ruang teori sebagai default
                    else {
                        $preferredRooms = $theoryRooms;
                    }
                } else {
                    // JIKA TIDAK ADA TAG SAMA SEKALI, berikan semua ruang teori sebagai default
                    $preferredRooms = $theoryRooms;
                }
            }

            if ($preferredRooms->isEmpty()) {
                continue;
            }

            // Tambahkan constraint ke file XML
            $cNode = $spaceList->addChild('ConstraintActivityPreferredRooms');
            $cNode->addChild('Weight_Percentage', '100');
            $cNode->addChild('Activity_Id', $activity->id);
            $cNode->addChild('Number_of_Preferred_Rooms', $preferredRooms->count());
            foreach ($preferredRooms as $room) {
                $cNode->addChild('Preferred_Room', htmlspecialchars($room->nama_ruangan));
            }
        }
    }

    private function saveXmlToFile(SimpleXMLElement $xml, ?Prodi $prodi, ?string $customFilePath, ?int $userId): string // <-- Terima $userId
    {
        if (is_null($customFilePath)) {
            $dirPath = storage_path('app/fet-generator/inputs');
            if (! file_exists($dirPath)) {
                mkdir($dirPath, 0775, true);
            }
            // Nama file digeneralisasi untuk fakultas
            $fileName = 'input_fakultas_'.time().'.fet';
            $customFilePath = $dirPath.'/'.$fileName;
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        $dom->save($customFilePath);
        FetFile::create([
            'link' => $customFilePath,
            'tipe' => 'fakultas',
            'tipe_id' => $userId,
        ]);
        return $customFilePath;
    }
}
