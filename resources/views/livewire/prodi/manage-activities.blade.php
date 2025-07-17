@php
    /**
     * @var \App\Models\Activity[]|\Illuminate\Contracts\Pagination\LengthAwarePaginator $activities
     * @var \App\Models\Subject[]|\Illuminate\Database\Eloquent\Collection $subjects
     * @var \App\Models\StudentGroup[]|\Illuminate\Database\Eloquent\Collection $allStudentGroups
     * @var \App\Models\Teacher[]|\Illuminate\Database\Eloquent\Collection $teachers
     * @var \App\Models\ActivityTag[]|\Illuminate\Database\Eloquent\Collection $activityTags
     * @var array $headers
     * @var bool $activityModal
     * @var int|null $activityId
     * @var array $teacher_ids
     */
@endphp

<div>
    <x-mary-toast />

    <div class="p-4">
        <x-mary-header title="Manajemen Aktivitas Pembelajaran" subtitle="Rangkai Dosen, Mata Kuliah, dan Kelompok Mahasiswa.">
            <x-slot:actions>
                <x-mary-button label="Tambah Aktivitas" icon="o-plus" @click="$wire.create()" class="btn-primary" />
            </x-slot:actions>
        </x-mary-header>

        <div class="flex flex-wrap -mx-3">
            <div class="w-full max-w-full px-3 mb-6 sm:w-2/4 sm:flex-none xl:mb-0 xl:w-2/4">
                <x-mary-input wire:model.live="searchActivities" label="Search activities"/>
            </div>
        </div>
        <hr/>
        {{-- Tabel Data --}}
        <x-mary-table :headers="$headers" :rows="$activities" with-pagination>
            @scope('cell_subject_display', $activity)
            <div class="flex items-center gap-2">
                <span class="font-semibold">{{ $activity->subject->nama_matkul }}</span>
                @if($activity->practicum_sks > 0)
                    <x-mary-badge value="+P" class="badge-info badge-xs" tooltip="Dengan Praktikum" />
                @endif
            </div>
            @endscope
            @scope('cell_student_group_names', $activity)
            <div class="flex flex-wrap gap-1">
                @forelse($activity->studentGroups as $group)
                    <x-mary-badge :value="$group->nama_kelompok . ', ' " class="badge-neutral" />
                @empty
                    <x-mary-badge value="-" class="badge-ghost" />
                @endforelse
            </div>
            @endscope
            @scope('cell_activity_tag.name', $activity)
            @if($activity->activityTag)
                <x-mary-badge :value="$activity->activityTag->name" class="badge-primary" />
            @else
                -
            @endif
            @endscope
            @scope('cell_teacher_names', $activity)
            {{-- Menggunakan accessor `teacher_names` yang sudah diurutkan dari model --}}
            {!! nl2br(e($activity->teacher_names)) !!}
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
                option-label="kode_name"
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

            {{-- Input Dosen Manual --}}
            <div>
                <label class="label font-semibold" for="teachers-list">Pilih Dosen</label>
                @php
                    // Kondisi diubah menjadi lebih sederhana.
                    // Logika ini sekarang akan berjalan setiap kali ada dosen yang dipilih ($teacher_ids tidak kosong),
                    // baik saat mode TAMBAH maupun EDIT.
                    if (!empty($teacher_ids)) {
                        $orderedIds = $teacher_ids;
                        [$selected, $unselected] = $teachers->partition(fn($teacher) => in_array($teacher->id, $orderedIds));
                        $sortedSelected = $selected->sortBy(fn($teacher) => array_search($teacher->id, $orderedIds));
                        $displayTeachers = $sortedSelected->merge($unselected);
                    } else {
                        // Jika tidak ada dosen yang dipilih, tampilkan daftar standar
                        $displayTeachers = $teachers;
                    }
                @endphp
                <div id="teachers-list" class="p-4 border rounded-lg max-h-60 overflow-y-auto space-y-2">
                    @forelse($displayTeachers as $teacher)
                        <x-mary-checkbox
                            :label="$teacher->nama_dosen"
                            :value="$teacher->id"
                            wire:click="toggleTeacherSelection({{ $teacher->id }})"
                            :checked="in_array($teacher->id, $teacher_ids)"
                        />
                    @empty
                        <p class="text-gray-500">Tidak ada dosen ditemukan.</p>
                    @endforelse
                </div>
                @if(!empty($teacher_ids))
                    <div class="mt-3 text-xs">
                        <span class="font-bold">Urutan Terpilih:</span>
                        <div class="flex flex-wrap gap-1 mt-1">
                            @foreach($teacher_ids as $id)
                                <x-mary-badge :value="$teachers->find($id)?->nama_dosen ?? '...'" class="badge-ghost" />
                            @endforeach
                        </div>
                    </div>
                @endif
                @error('teacher_ids') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

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
