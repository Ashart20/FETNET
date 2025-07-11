<div>
    <x-mary-toast />

    <div class="p-6 lg:p-8">
        <x-mary-header title="Manajemen Prodi, User, dan Cluster"
                       subtitle="Kelola data prodi dan user adminnya dari satu tempat." />

        <div class="my-4">
            <x-mary-button label="Tambah Prodi & User" @click="$wire.create()" class="btn-primary" icon="o-plus" />
        </div>

        {{-- Tabel Merry UI --}}
        <x-mary-table :headers="$this->headers()" :rows="$prodis" with-pagination>
            @scope('cell_users', $prodi)
            @forelse($prodi->users as $user)
                <div class="text-sm">
                    <div class="font-medium text-gray-800 dark:text-gray-200">{{ $user->name }}</div>
                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                </div>
            @empty
                <x-mary-badge value="Belum ada user" class="badge-error" />
            @endforelse
            @endscope

            @scope('actions', $prodi)
            <div class="flex space-x-2">
                <x-mary-button icon="o-pencil" @click="$wire.edit({{ $prodi->id }})" class="btn-sm btn-warning" />
                <x-mary-button icon="o-trash" wire:click="delete({{ $prodi->id }})" wire:confirm="PERHATIAN!|Anda yakin ingin menghapus prodi ini?|Aksi ini juga akan menghapus user yang terhubung." class="btn-sm btn-error" />
            </div>
            @endscope
        </x-mary-table>

        {{-- Modal untuk form tambah/edit --}}
        <x-mary-modal wire:model="prodiModal" title="{{ $prodiId ? 'Edit' : 'Tambah' }} Prodi & User" separator>
            <x-mary-form wire:submit="store">
                <div class="grid grid-cols-1 lg:grid-cols-1 gap-7">

                    <div class="space-y-4">
                        <x-mary-header title="Detail Program Studi" size="text-lg" with-separator />
                        <x-mary-input label="Nama Prodi" wire:model="nama_prodi" />
                        <x-mary-input label="Abbreviation" wire:model="abbreviation" placeholder="Contoh: TE" />
                        <x-mary-input label="Kode Prodi" wire:model="kode" />
                        {{-- Form kecil untuk menambah cluster baru --}}
                        <div class="p-4 border rounded-lg dark:border-gray-700 space-y-3">
                            <p class="text-sm font-bold text-gray-600 dark:text-gray-300">Tambah Cluster Baru</p>
                            @if(session('cluster-message'))
                                <x-mary-alert :description="session('cluster-message')" icon="o-check-circle" class="alert-success text-xs" />
                            @endif
                            <x-mary-input wire:model="newClusterName" label="Nama Cluster Baru" placeholder="Contoh:Departemen Pendidikan Elektro" />
                            <x-mary-input wire:model="newClusterCode" label="Kode Cluster Baru" placeholder="Contoh: DPTE" />
                            <x-mary-button label="Simpan Cluster" wire:click="addNewCluster" class="btn-primary btn-sm w-full" spinner="addNewCluster" />
                        </div>

                        {{-- Dropdown Cluster Standar --}}
                        <x-mary-select label="Cluster" :options="$clusters" wire:model="cluster_id" placeholder="-- Pilih Cluster --" />


                    </div>

                    <div class="space-y-4">
                        <x-mary-header title="Akun User Admin Prodi" size="text-lg" with-separator />
                        <x-mary-input label="Nama User" wire:model="name" />
                        <x-mary-input label="Email" wire:model="email" type="email" />
                        <x-mary-input label="Password" wire:model="password" type="password" placeholder="{{ $prodiId ? 'Kosongkan jika tidak diubah' : '' }}" />
                    </div>
                </div>

                {{-- Tombol Aksi di bagian bawah modal --}}
                <x-slot:actions>
                    <x-mary-button label="Batal" @click="$wire.closeModal()" />
                    <x-mary-button label="{{ $prodiId ? 'Update Data' : 'Simpan Semua' }}" type="submit" class="btn-primary" spinner="store" />
                </x-slot:actions>
            </x-mary-form>
        </x-mary-modal>
    </div>
</div>
