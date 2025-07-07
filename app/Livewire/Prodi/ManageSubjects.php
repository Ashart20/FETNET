<?php

namespace App\Livewire\Prodi;

use App\Models\Subject;
use Illuminate\Validation\Rule; // Import kelas Rule untuk validasi canggih
use Livewire\Component;
use Livewire\WithPagination;

class ManageSubjects extends Component
{
    use WithPagination;

    // Properti untuk form modal
    public ?int $subjectId = null;
    public string $nama_matkul = '';
    public string $kode_matkul = '';
    public ?int $sks = null;

    public bool $isModalOpen = false;

    /**
     * Mendefinisikan aturan validasi secara dinamis.
     */
    public function rules()
    {
        return [
            // PERBAIKAN: Validasi unique sekarang ada di nama_matkul, bukan kode_matkul
            'nama_matkul' => [
                'required',
                'string',
                'min:3',
                Rule::unique('subjects')->where('prodi_id', auth()->user()->prodi_id)->ignore($this->subjectId),
            ],
            'kode_matkul' => 'required|string|max:15',
            'sks' => 'required|integer|min:1|max:6',
        ];
    }

    /**
     * Pesan validasi kustom.
     */
    protected $messages = [
        'nama_matkul.required' => 'Nama mata kuliah wajib diisi.',
        'nama_matkul.unique'   => 'Nama mata kuliah ini sudah ada di prodi Anda.',
        'kode_matkul.required' => 'Kode mata kuliah wajib diisi.',
        'sks.required'         => 'Jumlah SKS wajib diisi.',
        'sks.integer'          => 'SKS harus berupa angka.',
    ];

    public function render()
    {
        $subjects = Subject::where('prodi_id', auth()->user()->prodi_id)
            ->latest()
            ->paginate(10);

        return view('livewire.prodi.manage-subjects', [
            'subjects' => $subjects
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

        Subject::updateOrCreate(['id' => $this->subjectId], $validatedData);

        session()->flash('message', $this->subjectId ? 'Data Mata Kuliah Berhasil Diperbarui.' : 'Data Mata Kuliah Berhasil Ditambahkan.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $subject = Subject::where('prodi_id', auth()->user()->prodi_id)->findOrFail($id);

        $this->subjectId = $id;
        $this->nama_matkul = $subject->nama_matkul;
        $this->kode_matkul = $subject->kode_matkul;
        $this->sks = $subject->sks;

        $this->openModal();
    }

    public function delete($id)
    {
        Subject::where('prodi_id', auth()->user()->prodi_id)->findOrFail($id)->delete();
        session()->flash('message', 'Data Mata Kuliah Berhasil Dihapus.');
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
