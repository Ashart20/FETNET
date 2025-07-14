<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\ActivityTag;
use App\Models\Building;
use App\Models\Cluster;
use App\Models\Day;
use App\Models\MasterRuangan;
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

class ClusterFetFileGeneratorService
{
    private const DEFAULT_WEIGHT = '100';

    public function generateForCluster(int $clusterId): string
    {
        $cluster = Cluster::findOrFail($clusterId);
        Log::info("Memulai pengambilan data untuk simulasi Cluster: {$cluster->name}.");

        $data = $this->fetchDataForCluster($cluster);
        Log::info('Pengambilan data selesai. Ditemukan '.$data['activities']->count().' aktivitas untuk cluster ini.');

        $xml = new SimpleXMLElement('<fet version="7.2.5"></fet>');
        $xml->addChild('Mode', 'Official');
        $xml->addChild('Institution_Name', "Simulasi Jadwal Cluster - {$cluster->name}");
        $xml->addChild('Comments', 'Dibuat secara otomatis oleh sistem pada '.now());

        $this->addDeclarations($xml, $data);
        $this->addConstraints($xml, $data);
        $xml->addChild('Timetable_Generation_Options_List');

        return $this->saveXmlToFile($xml, $cluster);
    }

    private function fetchDataForCluster(Cluster $cluster): array
    {
        $prodiIds = $cluster->prodis->pluck('id');

        if ($prodiIds->isEmpty()) {
            return array_fill_keys(['teachers', 'subjects', 'activities', 'rooms', 'buildings', 'days', 'timeSlots', 'teacherConstraints', 'roomConstraints', 'studentGroupConstraints', 'activityTags', 'studentGroups'], collect());
        }

        $teacherIds = Teacher::whereHas('prodis', fn ($q) => $q->whereIn('prodis.id', $prodiIds))->pluck('id');
        $studentGroupIds = StudentGroup::whereIn('prodi_id', $prodiIds)->pluck('id');

        return [
            // Data spesifik cluster
            'teachers' => Teacher::whereIn('id', $teacherIds)->get(),
            'subjects' => Subject::whereIn('prodi_id', $prodiIds)->with('prodi')->get(),
            'activities' => Activity::whereIn('prodi_id', $prodiIds)->with(['teachers', 'subject.prodi', 'studentGroups.prodi', 'activityTag', 'preferredRooms'])->get(),
            'studentGroups' => StudentGroup::whereIn('prodi_id', $prodiIds)->with('childrenRecursive.prodi', 'prodi')->whereNull('parent_id')->get(),
            'teacherConstraints' => TeacherTimeConstraint::whereIn('teacher_id', $teacherIds)->with(['teacher', 'day', 'timeSlot'])->get(),
            'studentGroupConstraints' => StudentGroupTimeConstraint::whereIn('student_group_id', $studentGroupIds)->with(['studentGroup.prodi', 'day', 'timeSlot'])->get(),

            'rooms' => MasterRuangan::with('building')->get(),
            'buildings' => Building::all(),
            'days' => Day::orderBy('id', 'asc')->get(),
            'timeSlots' => TimeSlot::orderBy('start_time')->get(),
            'activityTags' => ActivityTag::all(),
            'roomConstraints' => RoomTimeConstraint::with(['masterRuangan', 'day', 'timeSlot'])->get(),
        ];
    }

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

    private function addConstraints(SimpleXMLElement $xml, array $data): void
    {
        $this->addTimeConstraints($xml, $data);
        $this->addSpaceConstraints($xml, $data);
    }

    private function getUniqueStudentGroupName(StudentGroup $group): string
    {
        return optional($group->prodi)->kode.'-'.$group->nama_kelompok;
    }

    private function getUniqueSubjectName(Subject $subject): string
    {
        return optional($subject->prodi)->kode.'-'.$subject->nama_matkul;
    }

    private function addDaysList(SimpleXMLElement $xml, Collection $days): void
    {
        $list = $xml->addChild('Days_List');
        $list->addChild('Number_of_Days', $days->count());
        foreach ($days as $day) {
            $dayNode = $list->addChild('Day');
            $dayNode->addChild('Name', htmlspecialchars($day->name));
        }
    }

    private function addHoursList(SimpleXMLElement $xml, Collection $timeSlots): void
    {
        $list = $xml->addChild('Hours_List');
        $list->addChild('Number_of_Hours', $timeSlots->count());
        foreach ($timeSlots as $slot) {
            $hourNode = $list->addChild('Hour');
            $hourNode->addChild('Name', date('H:i', strtotime($slot->start_time)));
        }
    }

    private function addSubjectsList(SimpleXMLElement $xml, Collection $subjects): void
    {
        $list = $xml->addChild('Subjects_List');
        foreach ($subjects as $subject) {
            $node = $list->addChild('Subject');
            $node->addChild('Name', htmlspecialchars($this->getUniqueSubjectName($subject)));
        }
    }

    private function addActivityTagsList(SimpleXMLElement $xml, Collection $tags): void
    {
        $list = $xml->addChild('Activity_Tags_List');
        foreach ($tags as $tag) {
            $list->addChild('Activity_Tag')->addChild('Name', htmlspecialchars($tag->name));
        }
    }

    private function addTeachersList(SimpleXMLElement $xml, Collection $teachers): void
    {
        $list = $xml->addChild('Teachers_List');
        foreach ($teachers as $teacher) {
            $list->addChild('Teacher')->addChild('Name', htmlspecialchars($teacher->kode_dosen));
        }
    }

    private function addStudentsList(SimpleXMLElement $xml, Collection $years): void
    {
        $list = $xml->addChild('Students_List');
        foreach ($years as $year) {
            $yearNode = $list->addChild('Year');
            $yearNode->addChild('Name', htmlspecialchars($this->getUniqueStudentGroupName($year)));
            $yearNode->addChild('Number_of_Students', $year->jumlah_mahasiswa ?? 0);
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
        $groupNode->addChild('Name', htmlspecialchars($this->getUniqueStudentGroupName($group)));
        $groupNode->addChild('Number_of_Students', $group->jumlah_mahasiswa ?? 0);
        if ($group->childrenRecursive->isNotEmpty()) {
            foreach ($group->childrenRecursive as $subgroup) {
                $subgroupNode = $groupNode->addChild('Subgroup');
                $subgroupNode->addChild('Name', htmlspecialchars($this->getUniqueStudentGroupName($subgroup)));
                $subgroupNode->addChild('Number_of_Students', $subgroup->jumlah_mahasiswa ?? 0);
            }
        }
    }

    private function addActivitiesList(SimpleXMLElement $xml, Collection $activities): void
    {
        $activitiesList = $xml->addChild('Activities_List');
        foreach ($activities as $activity) {
            if ($activity->teachers->isEmpty() || ! $activity->subject || $activity->studentGroups->isEmpty()) {
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
            if ($activity->activityTag) {
                $activityNode->addChild('Activity_Tag', htmlspecialchars($activity->activityTag->name));
            }
        }
    }

    private function addBuildingsList(SimpleXMLElement $xml, Collection $buildings): void
    {
        $list = $xml->addChild('Buildings_List');
        foreach ($buildings as $building) {
            $list->addChild('Building')->addChild('Name', htmlspecialchars($building->code));
        }
    }

    private function addRoomsList(SimpleXMLElement $xml, Collection $rooms): void
    {
        $list = $xml->addChild('Rooms_List');
        foreach ($rooms as $room) {
            $node = $list->addChild('Room');
            $node->addChild('Name', htmlspecialchars($room->nama_ruangan));
            $node->addChild('Building', htmlspecialchars($room->building->code ?? ''));
            $node->addChild('Capacity', $room->kapasitas);
        }
    }

    private function addTimeConstraints(SimpleXMLElement $xml, array $data): void
    {
        $list = $xml->addChild('Time_Constraints_List');
        $list->addChild('ConstraintBasicCompulsoryTime')->addChild('Weight_Percentage', self::DEFAULT_WEIGHT);
        $this->addTeacherNotAvailableTimes($list, $data['teacherConstraints']);
        $this->addStudentNotAvailableTimes($list, $data['studentGroupConstraints']);
    }

    private function addSpaceConstraints(SimpleXMLElement $xml, array $data): void
    {
        $list = $xml->addChild('Space_Constraints_List');
        $list->addChild('ConstraintBasicCompulsorySpace')->addChild('Weight_Percentage', self::DEFAULT_WEIGHT);
        $this->addRoomNotAvailableTimes($list, $data['roomConstraints']);
        $this->addActivityPreferredRooms($list, $data['activities']);
    }

    private function addTeacherNotAvailableTimes(SimpleXMLElement $timeList, Collection $constraints): void
    {
        // Kelompokkan batasan berdasarkan ID guru
        $constraintsByTeacher = $constraints->groupBy('teacher_id');

        foreach ($constraintsByTeacher as $teacherId => $items) {
            $first = $items->first();
            if (! $first || ! $first->teacher) {
                continue;
            }

            $cNode = $timeList->addChild('ConstraintTeacherNotAvailableTimes');
            $cNode->addChild('Weight_Percentage', self::DEFAULT_WEIGHT);
            $cNode->addChild('Teacher', htmlspecialchars($first->teacher->kode_dosen));

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
        // Kelompokkan batasan berdasarkan ID kelompok mahasiswa
        $constraintsByGroup = $constraints->groupBy('student_group_id');

        foreach ($constraintsByGroup as $groupId => $items) {
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

    private function addRoomNotAvailableTimes(SimpleXMLElement $spaceList, Collection $constraints): void
    {
        foreach ($constraints->groupBy('master_ruangan_id') as $items) {
            $first = $items->first();
            if (! $first || ! $first->masterRuangan) {
                continue;
            }
            $cNode = $spaceList->addChild('ConstraintRoomNotAvailableTimes');
            $cNode->addChild('Room', htmlspecialchars($first->masterRuangan->nama_ruangan));
            foreach ($items as $item) {
                $notAvailableNode = $cNode->addChild('Not_Available_Time');
                $notAvailableNode->addChild('Day', $item->day->name);
                $notAvailableNode->addChild('Hour', date('H:i', strtotime($item->timeSlot->start_time)));
            }
        }
    }

    private function addActivityPreferredRooms(SimpleXMLElement $spaceList, Collection $activities): void
    {
        // Logika ini mungkin perlu disesuaikan jika tipe ruangan berbeda per cluster
        $allRooms = MasterRuangan::all();
        $labRooms = $allRooms->where('tipe', 'LABORATORIUM');
        $theoryRooms = $allRooms->where('tipe', 'KELAS_TEORI');

        foreach ($activities as $activity) {
            $preferredRooms = $activity->preferredRooms;
            if ($preferredRooms->isEmpty() && isset($activity->activityTag)) {
                $tagName = $activity->activityTag->name;
                if ($tagName === 'PRAKTIKUM') {
                    $preferredRooms = $labRooms;
                } elseif ($tagName === 'GANJIL' || $tagName === 'GENAP') {
                    $preferredRooms = $theoryRooms;
                }
            }
            if ($preferredRooms->isEmpty()) {
                continue;
            }

            $cNode = $spaceList->addChild('ConstraintActivityPreferredRooms');
            $cNode->addChild('Weight_Percentage', '95');
            $cNode->addChild('Activity_Id', $activity->id);

            $cNode->addChild('Number_of_Preferred_Rooms', $preferredRooms->count());

            foreach ($preferredRooms as $room) {
                $cNode->addChild('Preferred_Room', htmlspecialchars($room->nama_ruangan));
            }
        }
    }

    private function saveXmlToFile(SimpleXMLElement $xml, Cluster $cluster): string
    {
        $dirPath = storage_path('app/fet-files');
        if (! file_exists($dirPath)) {
            mkdir($dirPath, 0775, true);
        }
        $fileName = "simulasi_{$cluster->code}_".time().'.fet';
        $filePath = $dirPath.'/'.$fileName;
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        $dom->save($filePath);

        return $filePath;
    }
}
