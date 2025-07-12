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
            @scope('cell_student_group_names', $activity)
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
    <x-mary-modal class="modal-lg" wire:model="activityModal" title="{{ $activityId ? 'Edit' : 'Tambah' }} Aktivitas" box-class="w-400" separator>
            <div class="text-left">
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full max-w-full px-3 mb-6 sm:w-4/4 sm:flex-none xl:mb-0 xl:w-4/4">
                        <x-mary-select label="Pilih Mata Kuliah" wire:model="subject_id" :options="$subjects" option-label="nama_matkul" placeholder="-- Pilih --" />
                    </div>
                </div>
                <br/>
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full max-w-full px-3 mb-6 sm:w-4/4 sm:flex-none xl:mb-0 xl:w-4/4">
                        <x-mary-choices label="Pilih Kelompok Mahasiswa"
                                        wire:model="selectedStudentGroupIds"
                                        :options="$allStudentGroups"
                                        option-label="nama_kelompok"
                                        searchable
                                        multiple {{-- Penting: Aktifkan mode multi-pilih --}}
                                        placeholder="-- Pilih Kelompok --" />
                    </div>
                </div>
                <br/>
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full max-w-full px-3 mb-6 sm:w-4/4 sm:flex-none xl:mb-0 xl:w-4/4">
                        <x-mary-choices label="Pilih Dosen" wire:model="teacher_ids" :options="$teachers" option-label="nama_dosen" placeholder="-- Pilih Dosen --" /> searchable />
                    </div>
                </div>
                <br/>
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full max-w-full px-3 mb-6 sm:w-4/4 sm:flex-none xl:mb-0 xl:w-4/4">
                        <x-mary-select label="Tag Aktivitas (Wajib)" wire:model="activity_tag_id" :options="$activityTags" option-label="name" placeholder="-- Tidak ada --" />
                    </div>
                </div>
                <br/>
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full max-w-full px-3 mb-6 sm:w-4/4 sm:flex-none xl:mb-0 xl:w-4/4">
                        <x-mary-input label="Nama Aktivitas (Opsional)" wire:model="name" placeholder="..." />
                    </div>
                </div>
                <br/>
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full max-w-full px-3 mb-6 sm:w-2/4 sm:flex-none xl:mb-0 xl:w-2/4">
                        <x-mary-button label="Batal" @click="$wire.closeModal()" />
                        <x-mary-button label="{{ $activityId ? 'Update' : 'Simpan' }}" wire:click="store" class="btn-primary" spinner="store" />
                    </div>
                </div>
            </div>
    </x-mary-modal>
</div>
