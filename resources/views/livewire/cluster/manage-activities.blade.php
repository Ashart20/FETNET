<div>
    <x-mary-toast />

    <div class="p-4">
        <x-mary-header title="Manajemen Aktivitas (Cluster)" subtitle="Kelola semua aktivitas pembelajaran di dalam cluster Anda.">
            <x-slot:actions>
                <x-mary-button label="Tambah Aktivitas" icon="o-plus" @click="$wire.create()" class="btn-primary" />
            </x-slot:actions>
        </x-mary-header>

        {{-- Tabel Data --}}
        <x-mary-table :headers="$headers" :rows="$activities" with-pagination>
            {{-- Scope untuk menampilkan nama kelompok mahasiswa --}}
            @scope('cell_student_group_names', $activity)
            @forelse($activity->studentGroups as $group)
                <x-mary-badge :value="$group->nama_kelompok" class="badge-neutral mr-1 mb-1" />
            @empty
                <x-mary-badge value="-" class="badge-ghost" />
            @endforelse
            @endscope

            @scope('actions', $activity)
            <div class="flex gap-2">
                <x-mary-button icon="o-pencil" @click="$wire.edit({{ $activity->id }})" class="btn-sm btn-warning" spinner />
                <x-mary-button icon="o-trash" wire:click="delete({{ $activity->id }})" wire:confirm="Yakin menghapus aktivitas `{{ $activity->subject->nama_matkul }}`?" class="btn-sm btn-error" spinner />
            </div>
            @endscope
        </x-mary-table>

        {{-- BAGIAN BARU: Kartu untuk menampilkan Beban SKS Dosen --}}
        <div class="mt-8">
            <x-mary-card title="Akumulasi Beban SKS Dosen" subtitle="Total SKS yang diajarkan oleh setiap dosen di dalam cluster ini." shadow>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($teachers as $teacher)
                        @php
                            $sks = $teacherSksLoad[$teacher->id] ?? 0;
                            $colorClass = $sks > 12 ? 'bg-error text-error-content' : 'bg-base-200';
                        @endphp
                        <div class="flex justify-between items-center p-3 rounded-lg {{ $colorClass }}">
                            <span class="text-sm font-medium">{{ $teacher->nama_dosen }}</span>
                            <x-mary-badge :value="$sks . ' SKS'" class="badge-lg" />
                        </div>
                    @empty
                        <x-mary-alert title="Tidak ada data dosen" description="Belum ada dosen yang terdaftar di dalam cluster ini." icon="o-user-group" class="col-span-full" />
                    @endforelse
                </div>
            </x-mary-card>
        </div>
    </div>

    {{-- Modal Form --}}
    <x-mary-modal wire:model="activityModal" title="{{ $activityId ? 'Edit' : 'Tambah' }} Aktivitas" class="modal-lg" separator>
        <x-mary-form wire:submit="store">
            <div class="space-y-4 p-4">
                {{-- Pilihan prodi untuk user cluster --}}
                <x-mary-select label="Program Studi" wire:model="prodi_id" :options="$prodis" option-label="nama_prodi" placeholder="-- Pilih Prodi --" />
                <hr class="dark:border-gray-700" />
                <x-mary-select label="Pilih Mata Kuliah" wire:model="subject_id" :options="$subjects" option-label="nama_matkul" placeholder="-- Pilih --" />
                <x-mary-choices label="Pilih Kelompok Mahasiswa" wire:model="selectedStudentGroupIds" :options="$studentGroups" option-label="nama_kelompok" searchable />
                <x-mary-choices label="Pilih Dosen" wire:model="teacher_ids" :options="$teachers" option-label="nama_dosen" searchable />
                <x-mary-select label="Tag Aktivitas (Opsional)" wire:model="activity_tag_id" :options="$activityTags" option-label="name" placeholder="-- Tidak ada --" allow-clear />
                <x-mary-input label="Nama Aktivitas (Opsional)" wire:model="name" placeholder="Contoh: Praktikum Basis Data" />
            </div>

            <x-slot:actions>
                <x-mary-button label="Batal" @click="$wire.closeModal()" />
                <x-mary-button label="{{ $activityId ? 'Update' : 'Simpan' }}" type="submit" class="btn-primary" spinner="store" />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>
</div>
