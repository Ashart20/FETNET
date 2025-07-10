<?php

namespace App\Livewire\Prodi;

use App\Models\StudentGroup;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ManageStudentGroups extends Component
{
    // Properti untuk data
    public $groups;

    // Properti untuk form modal
    public ?int $studentGroupId = null;
    public ?int $parentId = null; // Untuk menyimpan parent saat menambah sub-kelompok

    public string $nama_kelompok = '';
    public string $kode_kelompok = '';
    public ?int $jumlah_mahasiswa = null;
    public string $angkatan = '';

    public bool $isModalOpen = false;

    /**
     * Aturan validasi dinamis yang benar untuk data hierarkis.
     */
    public function rules()
    {
        return [
            // Nama kelompok harus unik HANYA di dalam parent yang sama.
            'nama_kelompok' => [
                'required', 'string', 'min:3',
                Rule::unique('student_groups')
                    ->where('prodi_id', auth()->user()->prodi_id)
                    ->where('parent_id', $this->parentId)
                    ->ignore($this->studentGroupId),
            ],
            'kode_kelompok'    => 'nullable|string|max:15',
            'jumlah_mahasiswa' => 'nullable|integer|min:0',
            'angkatan'         => 'required|string|max:255',
        ];
    }

    protected $messages = [
        'nama_kelompok.required' => 'Nama kelompok/tingkat wajib diisi.',
        'nama_kelompok.unique'   => 'Nama ini sudah digunakan pada level yang sama.',
        'angkatan.required'      => 'Angkatan wajib diisi.',
    ];

    public function mount()
    {
        $this->loadGroups();
    }

    public function loadGroups()
    {
        // Ambil hanya level teratas (parent_id is null) dan muat semua turunannya secara rekursif
        $this->groups = StudentGroup::where('prodi_id', auth()->user()->prodi_id)
            ->whereNull('parent_id')
            ->with('childrenRecursive')
            ->orderBy('nama_kelompok')
            ->get();
    }

    public function render()
    {
        return view('livewire.prodi.manage-student-groups')->layout('layouts.app');
    }

    public function create($parentId = null)
    {
        $this->resetInputFields();
        $this->parentId = $parentId;
        $this->openModal();
    }

    public function store()
    {
        $validatedData = $this->validate();
        $validatedData['prodi_id'] = auth()->user()->prodi_id;
        $validatedData['parent_id'] = $this->parentId;

        StudentGroup::updateOrCreate(['id' => $this->studentGroupId], $validatedData);

        session()->flash('message', $this->studentGroupId ? 'Data berhasil diperbarui.' : 'Data berhasil ditambahkan.');
        $this->closeModal();
        $this->loadGroups(); // Muat ulang data pohon
    }

    public function edit($id)
    {
        $group = StudentGroup::where('prodi_id', auth()->user()->prodi_id)->findOrFail($id);

        $this->studentGroupId = $id;
        $this->parentId = $group->parent_id;
        $this->angkatan = $group->angkatan;
        $this->nama_kelompok = $group->nama_kelompok;
        $this->kode_kelompok = $group->kode_kelompok;
        $this->jumlah_mahasiswa = $group->jumlah_mahasiswa;

        $this->openModal();
    }

    public function delete($id)
    {
        // FindOrFail akan otomatis 404 jika tidak ditemukan
        $group = StudentGroup::where('prodi_id', auth()->user()->prodi_id)->with('childrenRecursive')->findOrFail($id);

        // Hapus semua turunan (sub-kelompok) terlebih dahulu secara rekursif
        $group->childrenRecursive()->delete();
        $group->delete();

        session()->flash('message', 'Data dan semua sub-kelompoknya berhasil dihapus.');
        $this->loadGroups(); // Muat ulang data pohon
    }

    public function openModal() { $this->isModalOpen = true; }
    public function closeModal() {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }
    private function resetInputFields()
    {
        $this->reset();
        $this->resetErrorBag();
        $this->loadGroups(); // Pastikan data tree selalu fresh saat reset
    }
}
