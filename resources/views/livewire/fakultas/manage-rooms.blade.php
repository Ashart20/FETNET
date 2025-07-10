<div>
    {{-- Komponen Toast untuk menampilkan notifikasi --}}
    <x-mary-toast />

    <div class="p-6 lg:p-8">
        {{-- Header halaman --}}
        <x-mary-header title="Manajemen Ruangan" subtitle="Kelola semua ruangan untuk penjadwalan." />
        <div class="my-3 flex flex-wrap gap-2">
            <x-mary-button label="Tambah Ruangan" icon="o-plus" class="btn-primary" @click="$wire.create()" />
            <x-mary-button label="Unduh Template Excel" icon="o-document-arrow-down" class="btn-secondary" wire:click="downloadTemplate" spinner />
        </div>

        <x-mary-table :headers="$this->headers()" :rows="$rooms" with-pagination>
            {{-- Menggunakan relasi untuk menampilkan nama gedung --}}
            @scope('cell_building.name', $room)
            {{ $room->building->name ?? 'N/A' }}
            @endscope

            {{-- Scope untuk tombol aksi (edit & hapus) --}}
            @scope('actions', $room)
            <div class="flex space-x-2">
                <x-mary-button icon="o-pencil" @click="$wire.edit({{ $room->id }})" class="btn-sm btn-warning" spinner />
                <x-mary-button
                    icon="o-trash"
                    wire:click="delete({{ $room->id }})"
                    wire:confirm="PERHATIAN!|Anda yakin ingin menghapus ruangan '{{ $room->nama_ruangan }}'?|Data yang terhubung mungkin akan terpengaruh."
                    class="btn-sm btn-error"
                    spinner />
            </div>
            @endscope
        </x-mary-table>

        <div class="my-6 p-4 bg-white dark:bg-gray-800/50 shadow-sm rounded-xl border dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-white">Impor Data dari Excel</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Unggah file .xlsx dengan header: `nama_ruangan`, `kode_ruangan`, `kode_gedung`, `lantai`, `kapasitas`, `tipe`.</p>

            <div class="mt-4">
                <x-mary-file wire:model.live="file" label="Pilih File Excel" hint="Hanya .xlsx" spinner />

                {{-- Indikator loading saat file sedang di-upload dan diproses --}}
                <div wire:loading wire:target="file" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Mengunggah dan memproses file...
                </div>

                @error('file') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

    </div>

    <x-mary-modal wire:model="roomModal" title="{{ $roomId ? 'Edit' : 'Tambah' }} Ruangan" separator>
        <x-mary-form wire:submit="store">
            <div class="space-y-4">
                {{-- Detail Ruangan --}}
                <x-mary-input label="Nama Ruangan" wire:model="nama_ruangan" />
                <x-mary-input label="Kode Ruangan" wire:model="kode_ruangan" />
                <x-mary-input label="Lantai" wire:model="lantai" placeholder="Contoh: 5" />
                <x-mary-input label="Kapasitas" wire:model="kapasitas" type="number" />
                <x-mary-select label="Tipe Ruangan" wire:model="tipe" :options="[
                    ['id' => 'KELAS_TEORI', 'name' => 'Kelas Teori'],
                    ['id' => 'LABORATORIUM', 'name' => 'Laboratorium'],
                    ['id' => 'AUDITORIUM', 'name' => 'Auditorium'],
                ]" />

                {{-- Form kecil untuk menambah gedung baru --}}
                <div class="p-4 border rounded-lg dark:border-gray-700 space-y-3 mt-4">
                    <p class="text-sm font-bold text-gray-600 dark:text-gray-300">Gedung tidak ada di daftar?</p>
                    <x-mary-input wire:model="newBuildingName" label="Nama Gedung Baru" placeholder="Contoh: Graha Pendidikan" />
                    <x-mary-input wire:model="newBuildingCode" label="Kode Gedung Baru" placeholder="Contoh: GP" />
                    <x-mary-button label="Simpan Gedung Baru" wire:click="addNewBuilding" class="btn-success btn-sm w-full" spinner="addNewBuilding" />
                </div>

                {{-- Dropdown Gedung --}}
                <x-mary-select label="Gedung" :options="$buildings" option-value="id" option-label="name" wire:model="building_id" placeholder="-- Pilih Gedung --" />
            </div>

            <x-slot:actions>
                <x-mary-button label="Batal" @click="$wire.closeModal()" />
                <x-mary-button label="{{ $roomId ? 'Update' : 'Simpan' }}" type="submit" class="btn-primary" spinner="store" />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>
</div>
