{{-- File: resources/views/livewire/prodi/manage-student-groups.blade.php --}}
<div>
    <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">

        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Manajemen Data Kelompok Mahasiswa</h1>

        <div class="my-3 flex gap-2">
            <x-mary-button label="Tambah Kelompok Baru" icon="o-plus" class="btn-primary" wire:click="create()" spinner />
        </div>

        {{-- Menggunakan komponen Mary UI Modal --}}
        <x-mary-modal wire:model="isModalOpen" title="{{ $studentGroupId ? 'Edit Kelompok Mahasiswa' : 'Tambah Kelompok Mahasiswa Baru' }}" subtitle="Lengkapi detail kelompok mahasiswa" separator>
            {{-- Isi modal diambil dari file terpisah --}}
            @include('livewire.prodi.student-group-modal')
            <x-slot:actions>
                <x-mary-button label="Batal" wire:click="closeModal" class="btn-ghost" />
                <x-mary-button label="{{ $studentGroupId ? 'Update' : 'Simpan' }}" wire:click="store" class="btn-primary" spinner="store" />
            </x-slot:actions>
        </x-mary-modal>

        {{-- Menggunakan komponen Mary UI Table --}}
        <x-mary-table :headers="$this->headers()" :rows="$studentGroups" striped @row-click="alert($event.detail.nama_kelompok)"> {{-- PERBAIKAN: event.detail.nama_kelompok --}}
            {{-- Customisasi kolom 'parent_group_name' agar menampilkan teks yang sudah diolah di Livewire --}}
            @scope('parent_group_name', $studentGroup)
            {{ $studentGroup->parent_group_name }}
            @endscope

            {{-- Customisasi kolom 'students_count' --}}
            @scope('students_count', $studentGroup)
            {{ $studentGroup->students_count }} Mahasiswa
            @endscope

            @scope('actions', $studentGroup)
            <x-mary-button icon="o-pencil-square" wire:click="edit({{ $studentGroup->id }})" class="btn-sm btn-warning" />
            <x-mary-button icon="o-trash" wire:click="delete({{ $studentGroup->id }})" wire:confirm="Anda yakin ingin menghapus data ini?" class="btn-sm btn-error" />
            @endscope
            {{-- Slot untuk jika tidak ada data --}}
            @empty($studentGroups)
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                        Belum ada data kelompok mahasiswa.
                    </td>
                </tr>
            @endempty
        </x-mary-table>

        <div class="mt-4">
            {{ $studentGroups->links() }}
        </div>
    </div>
</div>
