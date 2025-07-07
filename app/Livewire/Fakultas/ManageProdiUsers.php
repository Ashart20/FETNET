<?php

namespace App\Livewire\Fakultas;

use App\Models\Prodi;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class ManageProdiUsers extends Component
{
    use WithPagination;

    // Properti untuk form
    #[Rule('required|string|max:255')]
    public $name = '';

    #[Rule] // Aturan validasi dinamis akan diterapkan di method rules()
    public string $email = '';

    #[Rule] // Aturan validasi dinamis akan diterapkan di method rules()
    public $password = '';

    #[Rule('required|exists:prodis,id')]
    public $prodi_id = '';

    // Properti untuk state
    public $userId;
    public $isModalOpen = false;

    /**
     * Aturan validasi dinamis untuk menangani kasus 'create' vs 'update'.
     */
    public function rules()
    {
        $rules = [
            'email' => 'required|email|unique:users,email,' . $this->userId,
        ];

        // Jadikan password wajib hanya saat membuat user baru.
        if (!$this->userId) {
            $rules['password'] = 'required|min:8';
        } else {
            // Saat mengedit, password bersifat opsional.
            $rules['password'] = 'nullable|min:8';
        }

        return $rules;
    }

    /**
     * Pesan validasi kustom.
     */
    protected $messages = [
        'name.required' => 'Nama user wajib diisi.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email ini sudah terdaftar.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal 8 karakter.',
        'prodi_id.required' => 'Program studi wajib dipilih.',
    ];

    public function render()
    {
        $users = User::role('prodi')->with('prodi')->latest()->paginate(10);
        $prodis = Prodi::orderBy('nama_prodi')->get();

        return view('livewire.fakultas.manage-prodi-users', [
            'users' => $users,
            'prodis' => $prodis
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

        $data = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'prodi_id' => $this->prodi_id,
        ];

        // Hanya update password jika diisi
        if (!empty($validatedData['password'])) {
            $data['password'] = Hash::make($validatedData['password']);
        }

        $user = User::updateOrCreate(['id' => $this->userId], $data);

        // Berikan peran 'prodi' jika ini adalah user baru
        if (!$this->userId) {
            $user->assignRole('prodi');
        }

        session()->flash('message', $this->userId ? 'User Prodi berhasil diperbarui.' : 'User Prodi berhasil ditambahkan.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->prodi_id = $user->prodi_id;
        $this->password = ''; // Kosongkan password saat edit

        $this->openModal();
    }

    public function delete($userId)
    {
        User::destroy($userId);
        session()->flash('message', 'User Prodi berhasil dihapus.');
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
        // PERBAIKAN: Reset properti yang benar untuk komponen ini
        $this->reset(['name', 'email', 'password', 'prodi_id', 'userId']);
        $this->resetErrorBag();
    }
}
