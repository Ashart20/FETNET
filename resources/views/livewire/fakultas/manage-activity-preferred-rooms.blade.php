<div>
    {{-- Header Halaman --}}
    <div class="flex flex-wrap -mx-3">
        <div class="w-full max-w-full px-10 mb-6 sm:w-3/3 sm:flex-none xl:mb-0 xl:w-3/3">
            <x-mary-header title="Preferensi Ruangan untuk Aktivitas" subtitle="Atur ruangan mana saja yang diutamakan untuk setiap mata kuliah." />
        </div>
        <div class="w-full max-w-full px-10 mb-3 sm:w-1/3 sm:flex-none xl:mb-0 xl:w-1/3">
            <x-mary-select label="Pilih Prodi" wire:model.live="prodi_searchable_id" :options="$prodisSearchable" option-value="id" option-label="nama_prodi" placeholder="Pilih Prodi" searchable />
        </div>
        <div class="w-full max-w-full px-10 mb-4 sm:w-3/3 sm:flex-none xl:mb-0 xl:w-3/3">
            <x-mary-tabs wire:model.live="selectedTag" label="Filter Tipe Aktivitas">
                <x-mary-tab name="SEMUA" label="Semua" />
                <x-mary-tab name="KELAS_TEORI" label="Kelas Teori" />
                <x-mary-tab name="PRAKTIKUM" label="Praktikum" />
            </x-mary-tabs>
        </div>
    </div>
    {{-- Tabel Aktivitas --}}
    <div class="w-full max-w-full px-10 mt-4 mb-6 sm:w-3/3 sm:flex-none xl:mb-0 xl:w-3/3">
        <x-mary-table :headers="$this->headers()" :rows="$activities" with-pagination>
            @scope('cell_subject.nama_matkul', $activity)
            <div class="font-bold text-gray-800 dark:text-gray-200">{{ $activity->subject?->nama_matkul ?? 'N/A' }}</div>
            <div class="text-xs text-gray-500">{{ $activity->subject?->kode_matkul ?? '' }}</div>
            @endscope
            @scope('cell_prodi.nama_prodi', $activity)
            <div>{{ $activity->prodi?->nama_prodi ?? 'N/A' }}</div>
            @endscope
            @scope('cell_student_group_names', $activity)
            @forelse($activity->studentGroups as $group)
                <x-mary-badge :value="$group->nama_kelompok" class="badge-primary badge-outline" />
            @empty
                <x-mary-badge value="N/A" class="badge-error" />
            @endforelse
            @endscope
            @scope('cell_tipe_kelas', $activity)
            @if($activity->activityTag)
                @if($activity->activityTag->name == 'KELAS_TEORI')
                    <x-mary-badge value="Teori" class="badge-info badge-outline" />
                @elseif($activity->activityTag->name == 'PRAKTIKUM')
                    <x-mary-badge value="Praktikum" class="badge-success badge-outline" />
                @else
                    <x-mary-badge :value="$activity->activityTag->name" class="badge-ghost" />
                @endif
            @else
                <x-mary-badge value="N/A" class="badge-warning" />
            @endif
            @endscope
            @scope('cell_preferred_rooms', $activity)
            @forelse($activity->preferredRooms as $room)
                <x-mary-badge :value="$room->nama_ruangan.', kap: '.$room->kapasitas" class="badge-primary badge-outline" />
            @empty
                <x-mary-badge value="Belum diatur" class="badge-ghost" />
            @endforelse
            @endscope
            @scope('actions', $activity)
            <x-mary-button icon="o-home-modern" wire:click="editPreferences({{ $activity->id }})" label="Atur" class="btn-primary btn-sm" />
            @endscope
        </x-mary-table>
    </div>
    {{-- Modal Pengaturan Preferensi --}}
    <x-mary-modal wire:model="preferenceModal" title="Atur Preferensi Ruangan" separator box_class="max-w-4xl">
        @if($selectedActivity)
            <p class="mb-4">Mengatur preferensi untuk: <span class="font-bold text-primary">{{ $selectedActivity->subject?->nama_matkul ?? 'N/A' }}</span> - <span class="text-sm"> @forelse($selectedActivity->studentGroups as $group) {{ $group->nama_kelompok }}{{ !$loop->last ? ', ' : '' }} @empty N/A @endforelse </span></p>
            <x-mary-form wire:submit="savePreferences">
                <div class="p-4 border rounded-lg max-h-96 overflow-y-auto">
                    @if($allRooms && count($allRooms) > 0)
                        @foreach($allRooms as $tipe => $ruanganGroup)
                            <div class="mb-5">
                                <div class="font-bold text-lg border-b mb-2 pb-1">{{ $tipe }}</div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    @foreach($ruanganGroup as $room)
                                        {{-- DIUBAH: Menggunakan akses array --}}
                                        <x-mary-checkbox :label="$room['name_with_capacity']" wire:model.live="selectedRooms" value="{{ $room['id'] }}" />
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center text-gray-500">Tidak ada ruangan yang sesuai ditemukan.</p>
                    @endif
                </div>
                <x-slot:actions>
                    <x-mary-button label="Batal" @click="$wire.closeModal()" />
                    <x-mary-button label="Simpan Preferensi" type="submit" class="btn-primary" spinner="savePreferences" />
                </x-slot:actions>
            </x-mary-form>
        @endif
    </x-mary-modal>
</div>
