<?php

namespace App\Livewire\Fakultas;

use App\Models\Cluster;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Illuminate\View\View;

class ManageProdi extends Component
{
    use WithPagination, Toast;

    // Properti untuk Prodi
    public ?int $prodiId = null;
    public string $nama_prodi = '';
    public string $kode = '';
    public $cluster_id = '';

    // Properti untuk User Prodi
    public ?int $userId = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';

    // Properti untuk data & state
    public Collection $clusters;
    public bool $prodiModal = false;

    // Properti untuk form tambah cluster
    public string $newClusterName = '';
    public string $newClusterCode = '';

    public function mount(): void
    {
        $this->loadClusters();
    }

    public function loadClusters(): void
    {
        // Asumsi user fakultas tidak terikat prodi, jadi kita ambil semua cluster
        // Jika cluster harus terikat user, gunakan: Cluster::where('user_id', auth()->id())->...
        $this->clusters = Cluster::orderBy('name')->get();
    }

    // Method untuk mendefinisikan header tabel Merry UI
    public function headers(): array
    {
        return [
            ['key' => 'nama_prodi', 'label' => 'Nama Prodi'],
            ['key' => 'kode', 'label' => 'Kode'],
            ['key' => 'cluster.name', 'label' => 'Cluster'],
            ['key' => 'users', 'label' => 'User Terdaftar', 'sortable' => false],
        ];
    }

    public function render(): View
    {
        return view('livewire.fakultas.manage-prodi', [
            'prodis' => Prodi::with('cluster', 'users')->latest()->paginate(10)
        ])->layout('layouts.app');
    }

    // Method untuk menambah cluster baru dari dalam modal
    public function addNewCluster(): void
    {
        $validated = $this->validate([
            'newClusterName' => ['required', 'string', Rule::unique('clusters', 'name')->where('user_id', auth()->id())],
            'newClusterCode' => ['required', 'string', 'max:10', Rule::unique('clusters', 'code')->where('user_id', auth()->id())],
        ]);

        // PERBAIKAN: Hapus tanda komentar dari baris user_id
        $cluster = Cluster::create([
            'name' => $validated['newClusterName'],
            'code' => $validated['newClusterCode'],
            'user_id' => auth()->id(),
        ]);

        $this->loadClusters();
        $this->cluster_id = $cluster->id;
        $this->reset(['newClusterName', 'newClusterCode']);
        $this->toast(type: 'success', title: 'Cluster baru berhasil ditambahkan!');
    }

    public function create(): void
    {
        $this->resetInputFields();
        $this->prodiModal = true;
    }

    public function store(): void
    {
        $validatedData = $this->validate([
            'nama_prodi' => ['required', 'string', 'min:3'],
            'kode'       => ['required', 'string', 'max:10', Rule::unique('prodis')->ignore($this->prodiId)],
            'cluster_id' => ['nullable', 'exists:clusters,id'],
            'name'       => ['required', 'string'],
            'email'      => ['required', 'email', Rule::unique('users')->ignore($this->userId)],
            'password'   => [$this->prodiId ? 'nullable' : 'required', 'min:8'],
        ]);

        DB::transaction(function () use ($validatedData) {
            $prodiData = [
                'nama_prodi' => $validatedData['nama_prodi'],
                'kode'       => $validatedData['kode'],
                'cluster_id' => $validatedData['cluster_id'] ?: null,
            ];
            $prodi = Prodi::updateOrCreate(['id' => $this->prodiId], $prodiData);

            $userData = [
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'prodi_id' => $prodi->id,
            ];
            if (!empty($validatedData['password'])) {
                $userData['password'] = Hash::make($validatedData['password']);
            }
            $user = User::updateOrCreate(['id' => $this->userId], $userData);

            if (!$this->prodiId) {
                $user->assignRole('prodi');
            }
        });

        $this->toast(type: 'success', title: $this->prodiId ? 'Data berhasil diperbarui.' : 'Data berhasil ditambahkan.');
        $this->closeModal();
    }

    public function edit(Prodi $prodi): void
    {
        $this->resetInputFields();
        $user = $prodi->users->first();

        $this->prodiId = $prodi->id;
        $this->nama_prodi = $prodi->nama_prodi;
        $this->kode = $prodi->kode;
        $this->cluster_id = $prodi->cluster_id;

        if ($user) {
            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
        }

        $this->prodiModal = true;
    }

    public function delete(Prodi $prodi): void
    {
        $prodi->delete();
        $this->toast(type: 'success', title: 'Prodi Berhasil Dihapus.');
    }

    public function closeModal(): void
    {
        $this->prodiModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields(): void
    {
        $this->reset();
        $this->loadClusters();
        $this->resetErrorBag();
    }
}
