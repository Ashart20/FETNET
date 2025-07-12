<div>
    {{-- Toast untuk notifikasi --}}
    <x-mary-toast />

    <div class="p-4">

        <x-mary-header title="Manajemen Data Mata Kuliah" subtitle="Kelola semua mata kuliah untuk prodi Anda.">
            <x-slot:actions>
                <div class="flex flex-wrap gap-2">
                    <x-mary-button label="Tambah" icon="o-plus" class="btn-primary" @click="$wire.create()" />
                    <x-mary-button label="Unduh Template" icon="o-document-arrow-down" class="btn-info" wire:click="downloadTemplate" spinner />
                    <x-mary-button label="Hapus Semua" icon="o-trash" class="btn-error" wire:click="deleteAllSubjects"
                                   wire:confirm="PERHATIAN!|Anda yakin ingin menghapus SEMUA mata kuliah?|Aksi ini tidak bisa dibatalkan." />
                </div>
            </x-slot:actions>
        </x-mary-header>

        <x-mary-table :headers="$this->headers()" :rows="$subjects" with-pagination striped>
            @scope('actions', $subject)
            <div class="flex gap-2">
                <x-mary-button icon="o-pencil" wire:click="edit({{ $subject->id }})" class="btn-sm btn-warning" spinner />
                <x-mary-button icon="o-trash" wire:click="delete({{ $subject->id }})" wire:confirm="Yakin menghapus `{{ $subject->nama_matkul }}`?" class="btn-sm btn-error" spinner />
            </div>
            @endscope
        </x-mary-table>

        <div class="mt-8">
            <x-mary-card title="Impor Data dari Excel" icon="o-arrow-up-tray" shadow>
                <p class="text-sm text-gray-500 -mt-2 mb-4">Unggah file `.xlsx` atau `.xls` dengan kolom: `nama_matkul`, `kode_matkul`, `sks`.</p>
                <x-mary-file wire:model.live="file" label="Pilih File Excel" accept=".xlsx, .xls" hint="File akan langsung diproses." />
            </x-mary-card>
        </div>

    </div>

    {{-- Modal Form --}}
    <x-mary-modal wire:model="subjectModal" title="{{ $subjectId ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah' }}" separator>
        @include('livewire.prodi.subject-modal')
    </x-mary-modal>
</div>
