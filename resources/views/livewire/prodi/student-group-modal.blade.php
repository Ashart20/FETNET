{{-- File: resources/views/livewire/prodi/student-group-modal.blade.php --}}
<x-mary-form wire:submit.prevent="store">
    <div class="space-y-4">
        {{-- PERBAIKAN: wire:model="nama_kelompok" --}}
        <x-mary-input label="Nama Kelompok Mahasiswa" wire:model="nama_kelompok" placeholder="Contoh: Kelas A, Rombel 1" />
        {{-- PERBAIKAN: wire:model="angkatan" --}}
        <x-mary-input label="Angkatan" wire:model="angkatan" type="text" placeholder="Contoh: 2024" />

        <x-mary-select label="Bagian dari Kelompok"
                       wire:model="parent_id"
                       :options="$parentGroups"
                       option-value="id"
                       option-label="nama_kelompok" {{-- PERBAIKAN: Gunakan nama_kelompok untuk label --}}
                       placeholder="Pilih kelompok induk (opsional)"
                       hint="Pilih 'Tidak Ada' untuk kelompok utama." />

        <x-mary-select label="Bagian dari Kelompok"
                       wire:model="parent_id"
                       :options="$parentGroups->map(fn($g) => ['id' => $g->id, 'nama_kelompok' => $g->nama_kelompok . ($g->angkatan ? ' (' . $g->angkatan . ')' : '') ])"
                       option-value="id"
                       option-label="nama_kelompok"
                       placeholder="Pilih kelompok induk (opsional)"
                       hint="Pilih 'Tidak Ada' untuk kelompok utama." />
        --}}
    </div>
</x-mary-form>
