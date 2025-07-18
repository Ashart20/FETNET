<?php

namespace App\Livewire\Fakultas;

use App\Models\Activity;
use App\Models\MasterRuangan as Room;
use App\Models\Prodi;
use Illuminate\Support\Collection; // Import Collection
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class ManageActivityPreferredRooms extends Component
{
    use Toast, WithPagination;

    public bool $preferenceModal = false;
    public ?Activity $selectedActivity = null;
    public array $selectedRooms = [];
    public array $allRooms = []; // DIUBAH: Inisialisasi sebagai array kosong
    public $prodi_searchable_id = null;
    public $prodisSearchable;
    public string $selectedTag = 'SEMUA';
    public $searchActivities  = null;
    public function mount(): void
    {
        $this->search('');
    }

    public function search(string $value = ''): void
    {
        $selectedOption = Prodi::where('id', $this->prodi_searchable_id)->get();
        $this->prodisSearchable = Prodi::query()
            ->where(function($q) use ($value) {
                $q->where('nama_prodi', 'like', "%$value%")->orWhere('kode', 'like', "%$value%")->orWhere('abbreviation', 'like', "%$value%");
            })
            ->orderBy('nama_prodi', 'asc')
            ->take(50)->get()->merge($selectedOption);
    }
    public function removePreferredRoom(int $activityId, int $roomId): void
    {
        // Cari aktivitas yang sesuai
        $activity = Activity::find($activityId);

        if ($activity) {
            // `detach` digunakan untuk menghapus relasi many-to-many
            $activity->preferredRooms()->detach($roomId);
            $this->success('Preferensi ruangan berhasil dihapus.');
        } else {
            $this->error('Gagal menghapus, aktivitas tidak ditemukan.');
        }

    }
    public function headers(): array
    {
        return [['key' => 'id', 'label' => 'ID', 'class' => 'hidden'], ['key' => 'subject.nama_matkul', 'label' => 'Mata Kuliah'], ['key' => 'prodi.nama_prodi', 'label' => 'Prodi'], ['key' => 'student_group_names', 'label' => 'Kelompok Mahasiswa'], ['key' => 'tipe_kelas', 'label' => 'Tipe Kelas', 'class' => 'text-center'], ['key' => 'preferred_rooms', 'label' => 'Preferensi Ruangan', 'sortable' => false], ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-1'],];
    }

    public function editPreferences(Activity $activity): void
    {
        $this->selectedActivity = $activity->load('studentGroups', 'preferredRooms', 'activityTag');
        $activityType = $this->selectedActivity->activityTag?->name;
        if ($activityType) {
            $roomType = $activityType === 'PRAKTIKUM' ? 'LABORATORIUM' : 'KELAS_TEORI';
            $this->allRooms = Room::query()
                ->withCount('preferredByActivities')
                ->whereIn('tipe', [$roomType, 'UMUM'])->orderBy('tipe')->orderBy('nama_ruangan')->get()->groupBy('tipe')->toArray();
        } else {
            $this->allRooms = [];
        }
        $this->selectedRooms = $this->selectedActivity->preferredRooms->pluck('id')->toArray();
        $this->preferenceModal = true;
    }

    public function savePreferences(): void
    {
        $this->validate(['selectedRooms' => 'array', 'selectedRooms.*' => 'exists:master_ruangans,id',]);
        if ($this->selectedActivity) {
            $this->selectedActivity->preferredRooms()->sync($this->selectedRooms);
            $this->success('Preferensi ruangan berhasil diperbarui.');
            $this->closeModal();
        }
    }

    public function closeModal(): void
    {
        $this->preferenceModal = false;
        $this->reset('selectedActivity', 'selectedRooms');
    }

    public function render()
    {
        $activities = Activity::query()->with(['subject', 'prodi', 'studentGroups', 'preferredRooms', 'activityTag'])->when($this->prodi_searchable_id, function ($query) {
            $query->where('prodi_id', $this->prodi_searchable_id);
        })->when($this->selectedTag !== 'SEMUA', function ($query) {
            $query->whereHas('activityTag', function ($subQuery) {
                $subQuery->where('name', $this->selectedTag);
            });
        })->orderBy('subject_id')->paginate(10);

        if (!is_null($this->searchActivities)) {
            $search = $this->searchActivities;

            $activities = Activity::join('subjects', 'subjects.id', '=', 'activities.subject_id')
                ->join('activity_teacher', 'activity_teacher.activity_id', '=', 'activities.id')
                ->join('teachers', 'teachers.id', '=', 'activity_teacher.teacher_id')
                ->where(function ($query) use ($search) {
                    $query->where('subjects.nama_matkul', 'like', "%{$search}%")
                        ->orWhere('teachers.nama_dosen', 'like', "%{$search}%");
                })
                ->with(['teachers', 'subject', 'studentGroups', 'activityTag'])
                ->select('activities.*') // tetap pilih kolom activities saja
                ->groupBy('activities.id') // ganti distinct dengan groupBy kolom unik
                ->orderBy('subjects.kode_matkul')
                ->paginate(10);
                $this->resetPage();
        }
        return view('livewire.fakultas.manage-activity-preferred-rooms', ['activities' => $activities,])->layout('layouts.app');
    }
}
