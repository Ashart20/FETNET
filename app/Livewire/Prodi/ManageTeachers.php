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

    public bool $teacherModal = false;

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
                $prodiIdsInCluster = Prodi::where('cluster_id', $currentProdi->cluster_id)->pluck('id');
                $teachersQuery->whereHas('prodis', function ($query) use ($prodiIdsInCluster) {
                    $query->whereIn('prodis.id', $prodiIdsInCluster);
                })->distinct();
            } else {

                $teachersQuery->whereHas('prodis', function ($query) use ($currentProdi) {
                    $query->where('prodis.id', $currentProdi->id);
                });
            }
        } else {

            $teachers = $teachersQuery->whereRaw('1 = 0');
        }


        $teachers = $teachersQuery->orderBy('kode_dosen')->paginate(100);

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
        $prodiId = auth()->user()->prodi_id;

        $teacher = Teacher::updateOrCreate(['id' => $this->teacherId], [
            'nama_dosen' => $validatedData['nama_dosen'],
            'kode_dosen' => $validatedData['kode_dosen'],
        ]);

        $teacher->prodis()->syncWithoutDetaching([$prodiId]);

        $this->toast(type: 'success', title: $this->teacherId ? 'Data Dosen Berhasil Diperbarui.' : 'Data Dosen Berhasil Ditambahkan.');
        $this->closeModal();
    }

    public function edit($id)
    {
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

            $prodiId = auth()->user()->prodi_id;
            $teacher = Teacher::whereHas('prodis', fn($q) => $q->where('prodis.id', $prodiId))->findOrFail($id);

            $teacher->prodis()->detach($prodiId);


            if ($teacher->prodis()->count() === 0) {
                $teacher->delete();
            }
            $this->toast(type: 'warning', title: 'Data Dosen Berhasil Dihapus.');
        } catch (\Exception $e) {
            $this->error('Gagal menghapus dosen. Mungkin terhubung dengan data lain.');
        }
    }

    public function closeModal() {
        $this->teacherModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->reset();
        $this->resetErrorBag();
    }
}
