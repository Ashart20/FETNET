<?php

namespace App\Livewire\Fakultas;

use App\Models\Cluster;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Illuminate\View\View;

class ManageClusterUsers extends Component
{
    use WithPagination, Toast;

    // Properti untuk Modal & Form Cluster
    public bool $clusterModal = false;
    public ?int $clusterId = null;
    public string $clusterName = '';
    public string $clusterCode = '';

    // Properti untuk Modal & Form User
    public bool $userModal = false;
    public ?int $userId = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public $userClusterId = '';

    public Collection $clusters;

    public function mount(): void
    {
        $this->loadClusters();
    }

    public function loadClusters(): void
    {
        $this->clusters = Cluster::orderBy('name')->get();
    }

    public function clusterHeaders(): array
    {
        return [
            ['key' => 'name', 'label' => 'Nama Cluster'],
            ['key' => 'code', 'label' => 'Kode'],
            ['key' => 'prodis_count', 'label' => 'Jumlah Prodi'],
            ['key' => 'users_count', 'label' => 'Jumlah User'],
        ];
    }

    public function userHeaders(): array
    {
        return [
            ['key' => 'name', 'label' => 'Nama User'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'cluster.name', 'label' => 'Ditugaskan di Cluster'],
        ];
    }

    public function render(): View
    {
        $clusters = Cluster::withCount(['prodis', 'users' => function ($query) {
            $query->role('cluster');
        }])->latest()->paginate(5, ['*'], 'clustersPage');

        $users = User::role('cluster')->with('cluster')->latest()->paginate(5, ['*'], 'usersPage');

        return view('livewire.fakultas.manage-cluster-users', [
            'allClusters' => $clusters,
            'clusterUsers' => $users,
            'clusterHeaders' => $this->clusterHeaders(),
            'userHeaders' => $this->userHeaders(),
        ])->layout('layouts.app');
    }

    // --- METODE UNTUK CLUSTER ---

    public function createCluster(): void
    {
        $this->resetClusterFields();
        $this->clusterModal = true;
    }

    public function storeCluster(): void
    {
        $validated = $this->validate([
            'clusterName' => ['required', 'string', 'min:3', Rule::unique('clusters', 'name')->ignore($this->clusterId)],
            'clusterCode' => ['required', 'string', 'max:10', Rule::unique('clusters', 'code')->ignore($this->clusterId)],
        ]);

        // PERBAIKAN: Menambahkan 'user_id' saat membuat cluster baru.
        Cluster::updateOrCreate(
            ['id' => $this->clusterId],
            [
                'name' => $validated['clusterName'],
                'code' => $validated['clusterCode'],
                'user_id' => auth()->id() // Menambahkan ID user yang sedang login
            ]
        );

        $this->toast(type: 'success', title: $this->clusterId ? 'Cluster berhasil diperbarui.' : 'Cluster berhasil dibuat.');
        $this->closeModal();
    }

    public function editCluster(Cluster $cluster): void
    {
        $this->resetClusterFields();
        $this->clusterId = $cluster->id;
        $this->clusterName = $cluster->name;
        $this->clusterCode = $cluster->code;
        $this->clusterModal = true;
    }

    public function deleteCluster(Cluster $cluster): void
    {
        if ($cluster->prodis()->count() > 0 || $cluster->users()->role('cluster')->count() > 0) {
            $this->toast(type: 'error', title: 'Gagal!', description: 'Cluster tidak bisa dihapus karena masih memiliki prodi atau user.');
            return;
        }
        $cluster->delete();
        $this->toast(type: 'warning', title: 'Cluster berhasil dihapus.');
    }

    // --- METODE UNTUK USER ---

    public function createUser(): void
    {
        $this->resetUserFields();
        $this->userModal = true;
    }

    public function storeUser(): void
    {
        $validated = $this->validate([
            'name'       => ['required', 'string'],
            'email'      => ['required', 'email', Rule::unique('users')->ignore($this->userId)],
            'password'   => [$this->userId ? 'nullable' : 'required', 'min:8'],
            'userClusterId' => ['required', 'exists:clusters,id'],
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'cluster_id' => $validated['userClusterId'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user = User::updateOrCreate(['id' => $this->userId], $userData);
        $user->syncRoles(['cluster']);

        $this->toast(type: 'success', title: $this->userId ? 'User Cluster berhasil diperbarui.' : 'User Cluster berhasil dibuat.');
        $this->closeModal();
    }

    public function editUser(User $user): void
    {
        $this->resetUserFields();
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->userClusterId = $user->cluster_id;
        $this->userModal = true;
    }

    public function deleteUser(User $user): void
    {
        $user->delete();
        $this->toast(type: 'warning', title: 'User Cluster berhasil dihapus.');
    }

    // --- METODE UTILITAS ---

    public function closeModal(): void
    {
        $this->clusterModal = false;
        $this->userModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields(): void
    {
        $this->reset();
        $this->loadClusters();
        $this->resetErrorBag();
    }

    private function resetClusterFields(): void
    {
        $this->reset('clusterId', 'clusterName', 'clusterCode');
    }

    private function resetUserFields(): void
    {
        $this->reset('userId', 'name', 'email', 'password', 'userClusterId');
    }
}
