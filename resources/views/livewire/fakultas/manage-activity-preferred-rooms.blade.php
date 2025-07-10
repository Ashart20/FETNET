<div>
    {{-- Header Halaman --}}
    <x-mary-header title="Preferensi Ruangan untuk Aktivitas"
                   subtitle="Atur ruangan mana saja yang diutamakan untuk setiap mata kuliah." />

    {{-- Tabel Aktivitas --}}
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

        {{-- Scope untuk menampilkan nama kelompok mahasiswa --}}
        @scope('cell_studentGroup.nama_kelompok', $activity)
        <div>{{ $activity->studentGroup->nama_kelompok ?? 'N/A' }}</div>
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

    {{-- Modal Pengaturan Preferensi (tidak ada perubahan di sini) --}}
    <x-mary-modal wire:model="preferenceModal" title="Atur Preferensi Ruangan" separator>
        @if($selectedActivity)
            <p class="mb-4">
                Mengatur preferensi untuk:
                <span class="font-bold text-primary">{{ $selectedActivity->subject->nama_matkul ?? 'N/A' }}</span>
                -
                <span class="text-sm">{{ $selectedActivity->studentGroup->nama_kelompok ?? 'N/A' }}</span>
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
