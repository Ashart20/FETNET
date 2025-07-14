<?php

namespace App\Livewire\Prodi;

use App\Models\StudentGroup;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Mary\Traits\Toast;

class ManageStudentGroups extends Component
{
    use Toast;

    public $groups;

    public ?int $studentGroupId = null;

    public ?int $parentId = null;

    public string $nama_kelompok = '';

    public string $kode_kelompok = '';

    public ?int $jumlah_mahasiswa = null;

    public string $angkatan = '';

    public bool $studentGroupModal = false;

    public function rules()
    {
        return [
            'nama_kelompok' => [
                'required', 'string', 'min:3',
                Rule::unique('student_groups')
                    ->where('prodi_id', auth()->user()->prodi_id)
                    ->where('parent_id', $this->parentId)
                    ->ignore($this->studentGroupId),
            ],
            'kode_kelompok' => 'nullable|string|max:15',
            'jumlah_mahasiswa' => 'nullable|integer|min:0',
            'angkatan' => 'required|string|max:255',
        ];
    }

    protected $messages = [
        'nama_kelompok.required' => 'Nama kelompok/tingkat wajib diisi.',
        'nama_kelompok.unique' => 'Nama ini sudah digunakan pada level yang sama.',
        'angkatan.required' => 'Angkatan wajib diisi.',
    ];

    public function mount()
    {
        $this->loadGroups();
    }

    public function loadGroups()
    {
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
        $this->studentGroupModal = true;
    }

    public function store()
    {
        $validatedData = $this->validate();
        $validatedData['prodi_id'] = auth()->user()->prodi_id;
        $validatedData['parent_id'] = $this->parentId;

        StudentGroup::updateOrCreate(['id' => $this->studentGroupId], $validatedData);

        // Gunakan Toast untuk notifikasi
        $this->toast(type: 'success', title: $this->studentGroupId ? 'Data berhasil diperbarui.' : 'Data berhasil ditambahkan.');
        $this->closeModal();
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
        $this->studentGroupModal = true;
    }

    public function delete($id)
    {
        $group = StudentGroup::where('prodi_id', auth()->user()->prodi_id)->with('childrenRecursive')->findOrFail($id);
        $group->delete(); // Hapus parent akan otomatis menghapus children karena onDelete('cascade')
        $this->toast(type: 'warning', title: 'Data dan sub-kelompoknya berhasil dihapus.');
        $this->loadGroups();
    }

    public function closeModal()
    {
        $this->studentGroupModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->resetExcept('groups');
        $this->resetErrorBag();
        $this->loadGroups();
    }
}
