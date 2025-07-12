<div>
    {{-- Komponen Toast untuk notifikasi --}}
    <x-mary-toast />

    <div class="p-6 lg:p-8">
        {{-- Header halaman --}}
        <x-mary-header title="Manajemen Data Dosen" subtitle="Kelola semua data dosen di program studi Anda." />

        {{-- Tombol Aksi Utama (disamakan dengan manage-rooms) --}}
        <div class="my-3 flex flex-wrap gap-2">
            <x-mary-button label="Tambah Dosen" icon="o-plus" class="btn-primary" @click="$wire.create()" />
            <x-mary-button label="Unduh Template Excel" icon="o-document-arrow-down" class="btn-secondary" wire:click="downloadTemplate" spinner />
        </div>

        {{-- Tabel Data Dosen --}}
        <x-mary-table :headers="$headers" :rows="$teachers" with-pagination>
            {{-- Scope untuk menampilkan nama lengkap dengan gelar --}}
            @scope('cell_nama_dosen', $teacher)
            {{ $teacher->title_depan }} {{ $teacher->nama_dosen }} {{ $teacher->title_belakang }}
            @endscope

            {{-- Scope untuk tombol aksi di setiap baris --}}
            @scope('actions', $teacher)
            <div class="flex space-x-2">
                <x-mary-button icon="o-pencil" @click="$wire.edit({{ $teacher->id }})" class="btn-sm btn-warning" tooltip="Edit" />
                <x-mary-button
                    icon="o-trash"
                    wire:click="delete({{ $teacher->id }})"
                    wire:confirm="PERHATIAN!|Anda yakin ingin menghapus dosen ini dari prodi Anda?|Aksi ini tidak bisa dibatalkan."
                    class="btn-sm btn-error"
                    tooltip="Hapus" />
            </div>
            @endscope
        </x-mary-table>

        {{-- KOTAK FITUR IMPOR EXCEL (Style disamakan dengan manage-rooms) --}}
        <div class="my-6 p-4 bg-white dark:bg-gray-800/50 shadow-sm rounded-xl border dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-white">Impor Data Dosen dari Excel</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Unggah file .xlsx dengan header: `nama_dosen`, `kode_dosen`, `title_depan`, `title_belakang`, `kode_univ`, `employee_id`, `email`, `nomor_hp`.
            </p>

            <div class="mt-4">
                <x-mary-file wire:model.live="file" label="Pilih File Excel" hint="Hanya .xlsx" spinner />

                {{-- Indikator loading saat file sedang diproses --}}
                <div wire:loading wire:target="file" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Mengunggah dan memproses file...
                </div>
            </div>
        </div>

    </div>

    {{-- Modal untuk form tambah/edit (tidak ada perubahan) --}}
    <x-mary-modal wire:model="teacherModal" title="{{ $teacherId ? 'Edit' : 'Tambah' }} Data Dosen" separator>
        <x-mary-form wire:submit="store">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="space-y-4">
                    <x-mary-input label="Gelar Depan" wire:model="title_depan" placeholder="Contoh: Dr." />
                    <x-mary-input label="Nama Lengkap" wire:model="nama_dosen" placeholder="Masukkan nama tanpa gelar" />
                    <x-mary-input label="Gelar Belakang" wire:model="title_belakang" placeholder="Contoh: M.Kom." />
                    <x-mary-input label="Kode Dosen (Prodi)" wire:model="kode_dosen" placeholder="Contoh: BDO, RMD" />
                </div>
                <div class="space-y-4">
                    <x-mary-input label="NIDN / Kode Universitas" wire:model="kode_univ" placeholder="Masukkan NIDN" />
                    <x-mary-input label="Employee ID / NIP" wire:model="employee_id" placeholder="Masukkan NIP" />
                    <x-mary-input label="Email" wire:model="email" type="email" placeholder="dosen@email.com" />
                    <x-mary-input label="Nomor HP" wire:model="nomor_hp" placeholder="08123456789" />
                </div>
            </div>
            <x-slot:actions>
                <x-mary-button label="Batal" @click="$wire.closeModal()" />
                <x-mary-button label="{{ $teacherId ? 'Update Data' : 'Simpan' }}" type="submit" class="btn-primary" spinner="store" />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>
</div>
