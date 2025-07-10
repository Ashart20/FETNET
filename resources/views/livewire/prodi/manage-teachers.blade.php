<div>
    {{-- Komponen Toast untuk notifikasi --}}
    <x-mary-toast />

    <div class="p-6 lg:p-8">
        {{-- Header Halaman Mary UI --}}
        <x-mary-header title="Manajemen Data Dosen" subtitle="Kelola data dosen di program studi Anda.">
            {{-- Slot untuk tombol aksi di header --}}
            <x-slot:actions>
                <x-mary-button label="Tambah Dosen" @click="$wire.create()" class="btn-primary" icon="o-plus" />
            </x-slot:actions>
        </x-mary-header>

        {{-- Tabel Mary UI --}}
        <x-mary-table :headers="$headers" :rows="$teachers" with-pagination>
            {{-- Slot untuk tombol aksi di setiap baris --}}
            @scope('actions', $teacher)
            <div class="flex space-x-2">
                <x-mary-button icon="o-pencil" @click="$wire.edit({{ $teacher->id }})" class="btn-sm btn-warning" tooltip="Edit" />
                <x-mary-button icon="o-trash" wire:click="delete({{ $teacher->id }})" wire:confirm="PERHATIAN!|Anda yakin ingin menghapus data dosen ini?|Aksi ini tidak bisa dibatalkan." class="btn-sm btn-error" tooltip="Hapus" />
            </div>
            @endscope
        </x-mary-table>
    </div>

    {{-- Modal Mary UI untuk form --}}
    <x-mary-modal wire:model="teacherModal" title="{{ $teacherId ? 'Edit' : 'Tambah' }} Data Dosen" separator>
        <x-mary-form wire:submit="store">
            <div class="space-y-4">
                <x-mary-input label="Nama Lengkap Dosen" wire:model="nama_dosen" placeholder="Masukkan nama lengkap beserta gelar" class="input-bordered" />
                <x-mary-input label="Kode Dosen" wire:model="kode_dosen" placeholder="Contoh: BDO, RMD" class="input-bordered" />
            </div>

            {{-- Slot untuk tombol aksi di modal --}}
            <x-slot:actions>
                <x-mary-button label="Batal" @click="$wire.closeModal()" />
                <x-mary-button label="{{ $teacherId ? 'Update Data' : 'Simpan' }}" type="submit" class="btn-primary" spinner="store" />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>
</div>
