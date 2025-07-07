<?php

namespace App\Livewire\Prodi;

use App\Models\Teacher;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;

class ManageTeachers extends Component
{
    use WithPagination;

    // Properti untuk form
    public ?int $teacherId = null;

    #[Rule('required|string|min:3')]
    public string $nama_dosen = '';

    #[Rule] // Aturan dinamis akan didefinisikan di method rules()
    public string $kode_dosen = '';

    public bool $isModalOpen = false;

    /**
     * Aturan validasi dinamis untuk kode_dosen.
     */
    public function rules()
    {
        return [

            'kode_dosen' => 'required|string|max:10|unique:teachers,kode_dosen,' . $this->teacherId,
        ];
    }

    /**
     * Pesan validasi kustom.
     */
    protected $messages = [
        'nama_dosen.required' => 'Nama dosen wajib diisi.',
        'kode_dosen.required' => 'Kode dosen wajib diisi.',
        'kode_dosen.unique'   => 'Kode dosen ini sudah terdaftar.',
    ];

    public function render()
    {
        $teachers = Teacher::where('prodi_id', auth()->user()->prodi_id)
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
        $validatedData['prodi_id'] = auth()->user()->prodi_id;

        Teacher::updateOrCreate(['id' => $this->teacherId], $validatedData);

        session()->flash('message', $this->teacherId ? 'Data Dosen Berhasil Diperbarui.' : 'Data Dosen Berhasil Ditambahkan.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $teacher = Teacher::where('prodi_id', auth()->user()->prodi_id)->findOrFail($id);

        $this->teacherId = $id;
        $this->nama_dosen = $teacher->nama_dosen;
        $this->kode_dosen = $teacher->kode_dosen;

        $this->openModal();
    }

    public function delete($id)
    {
        // Gabungkan pemeriksaan otorisasi dan aksi hapus
        Teacher::where('prodi_id', auth()->user()->prodi_id)->findOrFail($id)->delete();
        session()->flash('message', 'Data Dosen Berhasil Dihapus.');
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
    }
}
