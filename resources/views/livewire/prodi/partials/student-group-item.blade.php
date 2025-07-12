{{--
    File: resources/views/livewire/prodi/partials/student-group-item.blade.php

    Komponen rekursif untuk menampilkan setiap kelompok dan sub-kelompoknya.
--}}

<div class="py-2" style="padding-left: {{ $level * 25 }}px;">
    <div class="flex items-center justify-between w-full p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800/50">

        {{-- Bagian Kiri: Nama Kelompok dan Jumlah Mahasiswa --}}
        <div class="flex items-center gap-3">
            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $group->nama_kelompok }}</span>

            {{-- PERUBAHAN DI SINI: Tampilkan badge jumlah mahasiswa jika ada --}}
            @if($group->jumlah_mahasiswa > 0)
                <x-mary-badge :value="$group->jumlah_mahasiswa" class="badge-primary badge-sm" icon="o-users" />
            @endif
        </div>

        {{-- Bagian Kanan: Tombol Aksi --}}
        <div class="flex items-center gap-2">
            <x-mary-button
                icon="o-pencil"
                @click="$wire.edit({{ $group->id }})"
                class="btn-xs btn-ghost text-yellow-500"
                tooltip="Edit" />
            <x-mary-button
                icon="o-plus"
                @click="$wire.create({{ $group->id }})"
                class="btn-xs btn-ghost text-green-500"
                tooltip="Tambah Sub-item" />
            <x-mary-button
                icon="o-trash"
                wire:click="delete({{ $group->id }})"
                wire:confirm="Yakin ingin menghapus '{{ $group->nama_kelompok }}' beserta semua sub-kelompoknya?"
                class="btn-xs btn-ghost text-red-500"
                tooltip="Hapus" />
        </div>
    </div>

    {{-- Panggil komponen ini lagi untuk setiap anak (children) --}}
    @if($group->childrenRecursive && $group->childrenRecursive->isNotEmpty())
        <div class="mt-1 border-l border-gray-200 dark:border-gray-700">
            @foreach($group->childrenRecursive as $child)
                @include('livewire.prodi.partials.student-group-item', ['group' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>
