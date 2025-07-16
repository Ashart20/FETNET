<div>
    {{-- Header Halaman --}}
    <x-mary-header title="Preferensi Ruangan untuk Aktivitas"
                   subtitle="Atur ruangan mana saja yang diutamakan untuk setiap mata kuliah." />

    {{-- Tabel Aktivitas --}}
    <div class="flex flex-wrap -mx-3">
        <div class="w-full max-w-full px-3 mb-6 sm:w-1/3 sm:flex-none xl:mb-0 xl:w-1/3">
        <x-mary-choices
            label="Select prodi"
            wire:model.live="prodi_searchable_id"
            :options="$prodiSearchable"
            search-function="search"
            debounce="300ms" {{-- Default is `250ms`--}}
            min-chars="2" {{-- Default is `0`--}}
            placeholder="Select prodi"
            single
            searchable>

            @scope('item', $prodis)
            <x-mary-list-item :item="$prodis">
                <x-slot:avatar>
                    <x-mary-icon name="o-user" class="bg-orange-100 p-2 w-8 h8 rounded-full"/>
                </x-slot:avatar>
                <x-slot:value>
                    {{$prodis->kode}}-{{$prodis->abbreviation}} :: {{$prodis->nama_prodi}}
                </x-slot:value>
            </x-mary-list-item>
            @endscope

            @scope('selection', $prodi)
            {{$prodi->kode}}-{{$prodi->abbreviation}} :: {{$prodi->nama_prodi}}
            @endscope
        </x-mary-choices>
        </div>
    </div>
    <br/>
    <x-mary-table :headers="$this->headers()" :rows="$activities" with-pagination>
        {{-- Scope untuk menampilkan nama mata kuliah dan kodenya --}}
        @scope('cell_subject.nama_matkul', $activity)
        <div class="font-bold text-gray-800 dark:text-gray-200">{{ $activity->subject->nama_matkul ?? 'N/A' }}</div>
        <div class="text-xs text-gray-500">{{ $activity->subject->kode_matkul ?? '' }}</div>
        @endscope

        {{-- Scope untuk menampilkan nama prodi --}}
        @scope('cell_prodi.nama_prodi', $activity)
        <div>{{ $activity->prodi->nama_prodi ?? 'N/A' }}</div>
        @endscope

        @scope('cell_student_group_names', $activity)
        @forelse($activity->studentGroups as $group)
            <x-mary-badge :value="$group->nama_kelompok" class="badge-neutral mr-1 mb-1" />
        @empty
            <x-mary-badge value="N/A (Kelompok tidak ditemukan)" class="badge-error" />
        @endforelse
        @endscope

        {{-- Scope untuk menampilkan preferensi ruangan dengan badge --}}
        @scope('cell_preferred_rooms', $activity)
        @forelse($activity->preferredRooms as $room)
            <x-mary-badge :value="$room->nama_ruangan" class="badge-primary badge-outline" />
        @empty
            <x-mary-badge value="Belum diatur" class="badge-ghost" />
        @endforelse
        @endscope

        {{-- Scope untuk tombol Aksi --}}
        @scope('actions', $activity)
        <x-mary-button icon="o-home-modern" wire:click="editPreferences({{ $activity->id }})" label="Atur" class="btn-primary btn-sm" />
        @endscope
    </x-mary-table>

    {{-- Modal Pengaturan Preferensi --}}
    <x-mary-modal wire:model="preferenceModal" title="Atur Preferensi Ruangan" separator>
        @if($selectedActivity)
            <p class="mb-4">
                Mengatur preferensi untuk:
                <span class="font-bold text-primary">{{ $selectedActivity->subject->nama_matkul ?? 'N/A' }}</span>
                -
                <span class="text-sm">
                    @forelse($selectedActivity->studentGroups as $group)
                        {{ $group->nama_kelompok }}{{ !$loop->last ? ', ' : '' }}
                    @empty
                        N/A (Kelompok tidak ditemukan)
                    @endforelse
                </span>
            </p>
            <x-mary-form wire:submit="savePreferences">
                <x-mary-choices
                    label="Pilih Ruangan"
                    wire:model="selectedRooms"
                    :options="$allRooms"
                    option-label="nama_ruangan"
                    searchable
                    multiple />

                <x-slot:actions>
                    <x-mary-button label="Batal" @click="$wire.closeModal()" />
                    <x-mary-button label="Simpan Preferensi" type="submit" class="btn-primary" spinner="savePreferences" />
                </x-slot:actions>
            </x-mary-form>
        @endif
    </x-mary-modal>
</div>
