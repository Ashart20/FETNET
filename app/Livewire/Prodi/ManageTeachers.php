<?php

namespace App\Livewire\Prodi;

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

    #[Rule]
    public string $kode_dosen = '';

    public bool $isModalOpen = false;

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

    public function render()
    {
        $prodiId = auth()->user()->prodi_id;

        // PERBAIKAN: Menggunakan whereHas untuk memfilter dosen berdasarkan prodi
        $teachers = Teacher::whereHas('prodis', function ($query) use ($prodiId) {
            $query->where('prodis.id', $prodiId);
        })
            ->latest()
            ->paginate(10);

        return view('livewire.prodi.manage-teachers', [
            'teachers' => $teachers
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function store()
    {
        $validatedData = $this->validate();
        $prodiId = auth()->user()->prodi_id;

        $teacher = Teacher::updateOrCreate(['id' => $this->teacherId], [
            'nama_dosen' => $validatedData['nama_dosen'],
            'kode_dosen' => $validatedData['kode_dosen'],
        ]);

        // Mengaitkan dosen dengan prodi yang sedang login
        // Gunakan syncWithoutDetaching untuk menjaga relasi lain jika dosen juga terkait prodi lain
        $teacher->prodis()->syncWithoutDetaching([$prodiId]);


        $this->toast(type: 'success', title: $this->teacherId ? 'Data Dosen Berhasil Diperbarui.' : 'Data Dosen Berhasil Ditambahkan.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $prodiId = auth()->user()->prodi_id;

        // PERBAIKAN: Memastikan dosen yang diedit adalah bagian dari prodi yang sedang login
        $teacher = Teacher::whereHas('prodis', function ($query) use ($prodiId) {
            $query->where('prodis.id', $prodiId);
        })->findOrFail($id);

        $this->teacherId = $id;
        $this->nama_dosen = $teacher->nama_dosen;
        $this->kode_dosen = $teacher->kode_dosen;

        $this->openModal();
    }

    public function delete($id)
    {
        try {
            $prodiId = auth()->user()->prodi_id;

            // Memastikan dosen yang dihapus adalah bagian dari prodi yang sedang login
            $teacher = Teacher::whereHas('prodis', function ($query) use ($prodiId) {
                $query->where('prodis.id', $prodiId);
            })->findOrFail($id);

            // Lepaskan relasi dosen dari prodi ini
            $teacher->prodis()->detach($prodiId);

            // Hapus dosen sepenuhnya hanya jika dia tidak terhubung dengan prodi lain
            if ($teacher->prodis()->count() === 0) {
                $teacher->delete();
            }

            $this->toast(type: 'warning', title: 'Data Dosen Berhasil Dihapus.');
        } catch (\Exception $e) {
            $this->error('Gagal menghapus dosen. Mungkin terhubung dengan aktivitas atau batasan lain, atau masih terkait prodi lain. ' . $e->getMessage());
        }
    }

    public function openModal() { $this->isModalOpen = true; }

    public function closeModal() {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->reset(); // Mereset semua properti, termasuk file
        $this->resetErrorBag();
    }
}
