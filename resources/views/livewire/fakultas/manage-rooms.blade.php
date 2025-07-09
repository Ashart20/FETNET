<div>
    {{-- Komponen Toast untuk menampilkan notifikasi --}}
    <x-mary-toast />

    <div class="p-6 lg:p-8">
        {{-- Header halaman --}}
        <x-mary-header title="Manajemen Ruangan" subtitle="Kelola semua ruangan untuk penjadwalan." />

        <div class="my-4 flex justify-between items-center">
            {{-- Tombol utama --}}
            <x-mary-button label="Tambah Ruangan" @click="$wire.create()" class="btn-primary" icon="o-plus" />

            {{-- Fitur Impor dan Download Template --}}
            <div class="flex items-center space-x-2">
                <x-mary-button label="Download Template" wire:click="downloadTemplate" icon="o-document-arrow-down" class="btn-sm btn-ghost" />

                {{-- Komponen file input dari MaryUI. `wire:model.live` akan memicu `updatedFile()` secara otomatis --}}
                <x-mary-file wire:model.live="file" label="Impor Excel" placeholder="Pilih file" class="w-48" spinner>
                    <x-slot:prepend>
                        <x-mary-icon name="o-arrow-up-tray" />
                    </x-slot:prepend>
                </x-mary-file>
            </div>
        </div>
        {{-- Tabel untuk menampilkan data ruangan --}}
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
    </div>

    {{-- ==================== MODAL FORM ==================== --}}
    <x-mary-modal wire:model="roomModal" title="{{ $roomId ? 'Edit' : 'Tambah' }} Ruangan" separator>
        {{-- Form di dalam modal --}}
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

                {{-- Form kecil untuk menambah gedung baru jika tidak ada di list --}}
                <div class="p-4 border rounded-lg dark:border-gray-700 space-y-3 mt-4">
                    <p class="text-sm font-bold text-gray-600 dark:text-gray-300">Gedung tidak ada di daftar?</p>
                    <x-mary-input wire:model="newBuildingName" label="Nama Gedung Baru" placeholder="Contoh: Graha Pendidikan" />
                    <x-mary-input wire:model="newBuildingCode" label="Kode Gedung Baru" placeholder="Contoh: GP" />
                    <x-mary-button label="Simpan Gedung Baru" wire:click="addNewBuilding" class="btn-success btn-sm w-full" spinner="addNewBuilding" />
                </div>

                {{-- Dropdown Gedung & Form Tambah Gedung --}}
                <x-mary-select label="Gedung" :options="$buildings" option-value="id" option-label="name" wire:model="building_id" placeholder="-- Pilih Gedung --" />


            </div>

            {{-- Tombol Aksi di bagian bawah modal --}}
            <x-slot:actions>
                <x-mary-button label="Batal" @click="$wire.closeModal()" />
                <x-mary-button label="{{ $roomId ? 'Update' : 'Simpan' }}" type="submit" class="btn-primary" spinner="store" />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>
</div>
