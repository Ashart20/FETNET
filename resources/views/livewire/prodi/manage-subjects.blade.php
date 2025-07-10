<div>
    <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">

        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Manajemen Data Mata Kuliah</h1>
        <div class="my-3 flex gap-2">
            <x-mary-button label="Tambah Mata Kuliah Baru" icon="o-plus" class="btn-primary" wire:click="create()" />
            <x-mary-button label="Hapus Semua" icon="o-trash" class="btn-error" wire:click="deleteAllSubjects"
                           wire:confirm="PERHATIAN! Anda yakin ingin menghapus SEMUA mata kuliah untuk prodi ini? Aksi ini tidak bisa dibatalkan." />
            <x-mary-button label="Unduh Template Excel" icon="o-document-arrow-down" class="btn-secondary" wire:click="downloadTemplate" spinner />
        </div>


        {{-- Menggunakan komponen Mary UI Modal --}}
        <x-mary-modal wire:model="subjectModal" title="{{ $subjectId ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah Baru' }}" subtitle="Lengkapi detail mata kuliah" separator>
            {{-- Isi modal dipindahkan ke sini atau tetap di include subject-modal.blade.php --}}
            @include('livewire.prodi.subject-modal')
            <x-slot:actions>
                <x-mary-button label="Batal" wire:click="closeModal" class="btn-ghost" />
                <x-mary-button label="Simpan" wire:click="store" class="btn-primary" spinner />
            </x-slot:actions>
        </x-mary-modal>

        {{-- Menggunakan komponen Mary UI Table --}}
        <x-mary-table :headers="$this->headers()" :rows="$subjects" striped @row-click="alert($event.detail.name)">
            @scope('actions', $subject)
            <x-mary-button icon="o-pencil-square" wire:click="edit({{ $subject->id }})" class="btn-sm btn-warning" />
            <x-mary-button icon="o-trash" wire:click="delete({{ $subject->id }})" wire:confirm="Anda yakin ingin menghapus data ini?" class="btn-sm btn-error" />
            @endscope
            {{-- Slot untuk jika tidak ada data --}}
            @empty($subjects)
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                        Belum ada data mata kuliah.
                    </td>
                </tr>
            @endempty
        </x-mary-table>


        <div class="mb-6 p-4 bg-white dark:bg-gray-800/50 shadow-sm rounded-xl border dark:border-gray-700 mt-4">
            <h3 class="font-semibold text-gray-900 dark:text-white">Impor Data dari Excel</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Unggah file .xlsx atau .xls dengan kolom: `nama_matkul`, `kode_matkul`, `sks`.</p>

            <div class="mt-4">
                {{-- Input ini sekarang langsung memicu proses impor secara otomatis --}}
                <x-mary-file wire:model="file" label="Pilih File Excel" accept=".xlsx, .xls" class="file-input-bordered" />

                {{-- Indikator loading saat file sedang di-upload dan diproses --}}
                <div wire:loading wire:target="file" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Mengunggah dan memproses file...
                </div>

                @error('file') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mt-4">
            {{ $subjects->links() }}
        </div>
    </div>
</div>
