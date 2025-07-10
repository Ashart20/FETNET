<?php

namespace App\Livewire\Prodi;

use App\Models\Prodi;
use App\Models\Teacher;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;
use Mary\Traits\Toast;

class ManageTeachers extends Component
{
    use WithPagination, Toast;

    public ?int $teacherId = null;

    #[Rule('required|string|min:3')]
    public string $nama_dosen = '';

    #[Rule('required|string|max:10')]
    public string $kode_dosen = '';

    // Mengganti nama properti modal agar sesuai dengan konvensi Mary UI
    public bool $teacherModal = false;

    // Aturan validasi dinamis
    public function rules()
    {
        return [
            'nama_dosen' => 'required|string|min:3',
            'kode_dosen' => 'required|string|max:10|unique:teachers,kode_dosen,' . $this->teacherId,
        ];
    }

    protected $messages = [
        'nama_dosen.required' => 'Nama dosen wajib diisi.',
        'kode_dosen.required' => 'Kode dosen wajib diisi.',
        'kode_dosen.unique'   => 'Kode dosen ini sudah terdaftar.',
    ];

    /**
     * Menambahkan method untuk mendefinisikan header tabel Mary UI.
     */
    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'nama_dosen', 'label' => 'Nama Dosen'],
            ['key' => 'kode_dosen', 'label' => 'Kode Dosen'],
        ];
    }

    public function render()
    {
        $currentProdi = auth()->user()->prodi;
        $teachersQuery = Teacher::query();

        if ($currentProdi) {
            if ($currentProdi->cluster_id) {
                // If prodi has a cluster, show teachers from all prodis in that cluster
                $prodiIdsInCluster = Prodi::where('cluster_id', $currentProdi->cluster_id)->pluck('id');
                $teachersQuery->whereHas('prodis', function ($query) use ($prodiIdsInCluster) {
                    $query->whereIn('prodis.id', $prodiIdsInCluster);
                })->distinct();
            } else {
                // If prodi does NOT have a cluster, show only teachers associated with this specific prodi
                $teachersQuery->whereHas('prodis', function ($query) use ($currentProdi) {
                    $query->where('prodis.id', $currentProdi->id);
                });
            }
        } else {
            // If there's no currentProdi (e.g., user not linked to any prodi), return an empty pagination
            // This is safer than directly returning an empty collection and trying to paginate it.
            $teachers = $teachersQuery->whereRaw('1 = 0'); // Ensures no records are returned
        }

        // Apply ordering and pagination to the query builder
        $teachers = $teachersQuery->latest('teachers.created_at')->paginate(10);

        // Mengirimkan data headers ke view
        return view('livewire.prodi.manage-teachers', [
            'teachers' => $teachers,
            'headers' => $this->headers()
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        // Mengubah properti yang dikontrol
        $this->teacherModal = true;
    }

    public function store()
    {
        $validatedData = $this->validate();
        $prodiId = auth()->user()->prodi_id; // Gets the current user's prodi_id

        $teacher = Teacher::updateOrCreate(['id' => $this->teacherId], [
            'nama_dosen' => $validatedData['nama_dosen'],
            'kode_dosen' => $validatedData['kode_dosen'],
        ]);

        $teacher->prodis()->syncWithoutDetaching([$prodiId]); // Attaches the teacher to the current user's prodi

        $this->toast(type: 'success', title: $this->teacherId ? 'Data Dosen Berhasil Diperbarui.' : 'Data Dosen Berhasil Ditambahkan.');
        $this->closeModal();
    }

    public function edit($id)
    {
        // Ensure the user can only edit teachers associated with their prodi
        $prodiId = auth()->user()->prodi_id;
        $teacher = Teacher::whereHas('prodis', fn($q) => $q->where('prodis.id', $prodiId))->findOrFail($id);

        $this->teacherId = $id;
        $this->nama_dosen = $teacher->nama_dosen;
        $this->kode_dosen = $teacher->kode_dosen;

        // Mengubah properti yang dikontrol
        $this->teacherModal = true;
    }

    public function delete($id)
    {
        try {
            // Ensure the user can only delete teachers associated with their prodi
            $prodiId = auth()->user()->prodi_id;
            $teacher = Teacher::whereHas('prodis', fn($q) => $q->where('prodis.id', $prodiId))->findOrFail($id);
            // Detach the teacher from the current prodi
            $teacher->prodis()->detach($prodiId);

            // If the teacher is no longer associated with any prodi, delete the teacher record entirely
            if ($teacher->prodis()->count() === 0) {
                $teacher->delete();
            }
            $this->toast(type: 'warning', title: 'Data Dosen Berhasil Dihapus.');
        } catch (\Exception $e) {
            $this->error('Gagal menghapus dosen. Mungkin terhubung dengan data lain.');
        }
    }

    public function closeModal() {
        // Mengubah properti yang dikontrol
        $this->teacherModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->reset();
        $this->resetErrorBag();
    }
}
