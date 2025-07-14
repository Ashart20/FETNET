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
            @scope('cell_subject_display', $activity)
            <div class="flex items-center gap-2">
                {{-- Tampilkan nama mata kuliah --}}
                <span>{{ $activity->subject->nama_matkul }}</span>

                {{-- Jika ada SKS praktikum, tampilkan badge +P --}}
                @if($activity->practicum_sks > 0)
                    <x-mary-badge value="+P" class="badge-info badge-xs" tooltip="Dengan Praktikum" />
                @endif
            </div>
            @endscope
            @scope('cell_student_group_names', $activity)
            @forelse($activity->studentGroups as $group)
                <x-mary-badge :value="$group->nama_kelompok" class="badge-neutral mr-1 mb-1" />
            @empty
                <x-mary-badge value="-" class="badge-ghost" />
            @endforelse
            @endscope
            @scope('cell_activity_tag.name', $activity)
            @if($activity->activityTag)
                <x-mary-badge :value="$activity->activityTag->name" class="badge-primary" />
            @else
                -
            @endif
            @endscope
            @scope('cell_teacher_names', $activity)
            {{ $activity->teachers->pluck('full_name')->implode(', ') }}
            @endscope

            @scope('actions', $activity)
            <div class="flex items-center space-x-2">
                <x-mary-button icon="o-pencil" wire:click="edit({{ $activity->id }})" class="btn-sm btn-warning" spinner />
                <x-mary-button icon="o-trash" wire:click="delete({{ $activity->id }})" wire:confirm="Yakin menghapus aktivitas ini?" class="btn-sm btn-error" spinner />
            </div>
            @endscope
        </x-mary-table>
    </div>

    {{-- Modal Form --}}
    <x-mary-modal class="backdrop-blur" wire:model="activityModal" title="{{ $activityId ? 'Edit' : 'Tambah' }} Aktivitas" separator>
        <div class="space-y-5">
            <x-mary-select
                label="Pilih Mata Kuliah"
                wire:model="subject_id"
                :options="$subjects"
                option-value="id"
                option-label="kode_name"  {{-- Cukup panggil accessor yang baru dibuat --}}
                placeholder="-- Pilih Mata Kuliah --"
                searchable
                required
            />

            <x-mary-choices
                label="Pilih Kelompok Mahasiswa"
                wire:model="selectedStudentGroupIds"
                :options="$allStudentGroups"
                option-label="nama_kelompok"
                searchable
                multiple
                placeholder="-- Pilih Kelompok --"
                required />

            <x-mary-choices
                label="Pilih Dosen"
                wire:model="teacher_ids"
                :options="$teachers"
                option-label="nama_dosen"
                placeholder="-- Pilih Dosen --"
                searchable
                multiple
                required />

            <x-mary-select
                label="Tag Aktivitas (Opsional)"
                wire:model="activity_tag_id"
                :options="$activityTags"
                option-label="name"
                placeholder="-- Pilih Tag --"
                allow-clear />

            <x-mary-input
                label="SKS Tambahan (Praktikum)"
                wire:model="practicum_sks"
                type="number"

            />
        </div>

        <x-slot:actions>
            <x-mary-button label="Batal" @click="$wire.closeModal()" />
            <x-mary-button label="{{ $activityId ? 'Update' : 'Simpan' }}" wire:click="store" class="btn-primary" spinner="store" />
        </x-slot:actions>
    </x-mary-modal>
</div>
