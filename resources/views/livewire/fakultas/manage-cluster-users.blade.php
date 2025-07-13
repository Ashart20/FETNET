<div>
    <x-mary-toast />

    <div class="p-4">
        <x-mary-header title="Manajemen Cluster & User" subtitle="Kelola data cluster dan akun koordinatornya." />

        <div class="grid grid-cols-1 gap-8 mt-4">
            {{-- KARTU 1: MANAJEMEN CLUSTER --}}
            <x-mary-card title="Daftar Cluster" shadow>
                <x-slot:actions>
                    <x-mary-button label="Tambah Cluster" icon="o-plus" @click="$wire.createCluster()" class="btn-primary" />
                </x-slot:actions>

                <x-mary-table :headers="$clusterHeaders" :rows="$allClusters" with-pagination>
                    @scope('actions', $cluster)
                    <div class="flex gap-2">
                        <x-mary-button icon="o-pencil" @click="$wire.editCluster({{ $cluster->id }})" class="btn-sm btn-warning" spinner />
                        <x-mary-button icon="o-trash" wire:click="deleteCluster({{ $cluster->id }})" class="btn-sm btn-error" spinner />
                    </div>
                    @endscope
                </x-mary-table>
            </x-mary-card>

            {{-- KARTU 2: MANAJEMEN USER CLUSTER --}}
            <x-mary-card title="Daftar User Cluster" shadow>
                <x-slot:actions>
                    <x-mary-button label="Tambah User Cluster" icon="o-user-plus" @click="$wire.createUser()" class="btn-primary" />
                </x-slot:actions>

                <x-mary-table :headers="$userHeaders" :rows="$clusterUsers" with-pagination>
                    @scope('actions', $user)
                    <div class="flex gap-2">
                        <x-mary-button icon="o-pencil" @click="$wire.editUser({{ $user->id }})" class="btn-sm btn-warning" spinner />
                        <x-mary-button icon="o-trash" wire:click="deleteUser({{ $user->id }})" wire:confirm="Yakin menghapus user `{{ $user->name }}`?" class="btn-sm btn-error" spinner />
                    </div>
                    @endscope
                </x-mary-table>
            </x-mary-card>
        </div>
    </div>

    {{-- MODAL 1: Form untuk Cluster --}}
    <x-mary-modal wire:model="clusterModal" title="{{ $clusterId ? 'Edit' : 'Tambah' }} Cluster" separator>
        <x-mary-form wire:submit="storeCluster">
            <div class="space-y-4">
                <x-mary-input label="Nama Cluster" wire:model="clusterName" class="input-bordered" />
                <x-mary-input label="Kode Cluster" wire:model="clusterCode" class="input-bordered" />
            </div>
            <x-slot:actions>
                <x-mary-button label="Batal" @click="$wire.closeModal()" />
                <x-mary-button label="{{ $clusterId ? 'Update' : 'Simpan' }}" type="submit" class="btn-primary" spinner="storeCluster" />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>

    {{-- MODAL 2: Form untuk User Cluster --}}
    <x-mary-modal wire:model="userModal" title="{{ $userId ? 'Edit' : 'Tambah' }} User Cluster" separator>
        <x-mary-form wire:submit="storeUser">
            <div class="space-y-4">
                <x-mary-input label="Nama Lengkap" wire:model="name" class="input-bordered" />
                <x-mary-input label="Email" wire:model="email" type="email" class="input-bordered" />
                <x-mary-input label="Password" wire:model="password" type="password" placeholder="{{ $userId ? 'Kosongkan jika tidak diubah' : '' }}" class="input-bordered" />
                <x-mary-select label="Tugaskan ke Cluster" wire:model="userClusterId" :options="$clusters" option-value="id" option-label="name" placeholder="-- Pilih Cluster --" class="select-bordered" />
            </div>
            <x-slot:actions>
                <x-mary-button label="Batal" @click="$wire.closeModal()" />
                <x-mary-button label="{{ $userId ? 'Update' : 'Simpan' }}" type="submit" class="btn-primary" spinner="storeUser" />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>
</div>
