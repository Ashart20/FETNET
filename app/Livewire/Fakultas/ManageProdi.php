<?php

namespace App\Livewire\Fakultas;

use App\Models\Prodi;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Rule; // Gunakan atribut Rule dari Livewire 3

class ManageProdi extends Component
{
    use WithPagination;

    // Properti untuk form, di-reset setelah submit
    #[Rule('required|string|min:3')]
    public $nama_prodi = '';

    // PERBAIKAN: Gunakan 'kode' bukan 'kode_prodi'
    // Aturan validasi yang lebih canggih untuk menangani 'unique' saat update
    #[Rule]
    public $kode = '';

    public $prodiId;

    // Properti untuk mengelola state modal
    public $isModalOpen = false;

    /**
     * Aturan validasi dinamis untuk kolom 'kode'.
     */
    public function rules()
    {
        return [
            'nama_prodi' => 'required|string|min:3',
            // Aturan unique ini akan mengabaikan prodi dengan ID saat ini,
            // sehingga tidak terjadi error "kode sudah ada" saat mengedit prodi itu sendiri.
            'kode' => 'required|string|max:10|unique:prodis,kode,' . $this->prodiId,
        ];
    }

    /**
     * Pesan validasi kustom dalam Bahasa Indonesia.
     */
    public function messages()
    {
        return [
            'nama_prodi.required' => 'Nama prodi wajib diisi.',
            'kode.required' => 'Kode prodi wajib diisi.',
            'kode.unique' => 'Kode prodi ini sudah digunakan.',
        ];
    }

    public function render()
    {
        return view('livewire.fakultas.manage-prodi', [
            'prodis' => Prodi::latest()->paginate(10)
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

        // PERBAIKAN: Gunakan 'kode' bukan 'kode_prodi'
        Prodi::updateOrCreate(['id' => $this->prodiId], [
            'nama_prodi' => $validatedData['nama_prodi'],
            'kode' => $validatedData['kode'],
        ]);

        session()->flash('message', $this->prodiId ? 'Prodi Berhasil Diperbarui.' : 'Prodi Berhasil Ditambahkan.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $prodi = Prodi::findOrFail($id);
        $this->prodiId = $id;
        $this->nama_prodi = $prodi->nama_prodi;
        // PERBAIKAN: Gunakan 'kode' bukan 'kode_prodi'
        $this->kode = $prodi->kode;

        $this->openModal();
    }

    public function delete($id)
    {
        // PERBAIKAN: Gunakan `destroy` lebih aman dan ringkas
        Prodi::destroy($id);
        session()->flash('message', 'Prodi Berhasil Dihapus.');
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->reset(['nama_prodi', 'kode', 'prodiId']);
    }
}
