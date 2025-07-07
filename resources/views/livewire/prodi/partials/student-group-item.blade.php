<div class="py-2 {{ $level > 0 ? 'ml-'.($level * 4) : '' }}">
    <div class="flex items-center justify-between p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700/50">
        {{-- Informasi Grup --}}
        <div class="flex-1">
            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $group->nama_kelompok }}</span>
            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">(Angkatan: {{ $group->angkatan }})</span>
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex items-center space-x-2">
            {{-- Tombol Tambah Sub-kelompok hanya muncul untuk Tingkat dan Kelompok --}}
            @if($level < 2)
                <button wire:click="create({{ $group->id }})" class="text-green-500 hover:text-green-700 text-xs font-bold">Tambah Sub</button>
            @endif
            <button wire:click="edit({{ $group->id }})" class="text-yellow-500 hover:text-yellow-700 text-xs font-bold">Edit</button>
            <button wire:click="delete({{ $group->id }})" wire:confirm="Yakin ingin menghapus '{{ $group->nama_kelompok }}' beserta semua sub-kelompoknya?" class="text-red-500 hover:text-red-700 text-xs font-bold">Hapus</button>
        </div>
    </div>

    {{-- Panggil komponen ini lagi untuk setiap turunan (children) --}}
    @if($group->childrenRecursive->isNotEmpty())
        <div class="border-l-2 dark:border-gray-600">
            @foreach($group->childrenRecursive as $child)
                @include('livewire.prodi.partials.student-group-item', ['group' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>
