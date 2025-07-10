<div>
    <x-mary-toast />

    <div class="p-4">
        <x-mary-header title="Manajemen Aktivitas Pembelajaran" subtitle="Rangkai Dosen, Mata Kuliah, dan Kelompok Mahasiswa.">
            <x-slot:actions>
                <x-mary-button label="Tambah Aktivitas" icon="o-plus" @click="$wire.create()" class="btn-primary" />
            </x-slot:actions>
        </x-mary-header>

        {{-- Tabel Data --}}
        <x-mary-table :headers="$headers" :rows="$activities" with-pagination>
            @scope('cell_student_group_names', $activity) {{-- Perubahan: Menggunakan key baru untuk kolom kelompok --}}
            @forelse($activity->studentGroups as $group)
                <x-mary-badge :value="$group->nama_kelompok" class="badge-neutral mr-1 mb-1" />
            @empty
                <x-mary-badge value="Tidak ada kelompok" class="badge-error" />
            @endforelse
            @endscope

            @scope('actions', $activity)
            <div class="flex gap-2">
                <x-mary-button icon="o-pencil" @click="$wire.edit({{ $activity->id }})" class="btn-sm btn-warning" spinner />
                <x-mary-button icon="o-trash" wire:click="delete({{ $activity->id }})" wire:confirm="Yakin menghapus aktivitas ini?" class="btn-sm btn-error" spinner />
            </div>
            @endscope
        </x-mary-table>
    </div>

    {{-- Modal Form --}}
    <x-mary-modal wire:model="activityModal" title="{{ $activityId ? 'Edit' : 'Tambah' }} Aktivitas" separator>
        <x-mary-form wire:submit="store">
            <div class="space-y-4">
                <x-mary-choices label="Pilih Dosen" wire:model="teacher_ids" :options="$teachers" option-label="nama_dosen" searchable />
                <x-mary-select label="Pilih Mata Kuliah" wire:model="subject_id" :options="$subjects" option-label="nama_matkul" placeholder="-- Pilih --" />


                <x-mary-choices label="Pilih Kelompok Mahasiswa"
                                wire:model="selectedStudentGroupIds"
                                :options="$allStudentGroups"
                                option-label="nama_kelompok"
                                searchable
                                multiple {{-- Penting: Aktifkan mode multi-pilih --}}
                                placeholder="-- Pilih Kelompok --" />

                <x-mary-select label="Tag Aktivitas (Opsional)" wire:model="activity_tag_id" :options="$activityTags" option-label="name" placeholder="-- Tidak ada --" />
                <x-mary-input label="Nama Aktivitas (Opsional)" wire:model="name" placeholder="Contoh: Kuliah Gabungan A" />

            </div>

            <x-slot:actions>
                <x-mary-button label="Batal" @click="$wire.closeModal()" />
                <x-mary-button label="{{ $activityId ? 'Update' : 'Simpan' }}" type="submit" class="btn-primary" spinner="store" />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>
</div>
