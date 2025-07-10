<div class="py-2 {{ $level > 0 ? 'ml-'.($level * 4) : '' }}">
    <div class="flex items-center justify-between p-2 rounded-md hover:bg-base-200">
        {{-- Informasi Grup --}}
        <div class="flex-1">
            <span class="font-semibold">{{ $group->nama_kelompok }}</span>
            <span class="text-xs opacity-60 ml-2">(Angkatan: {{ $group->angkatan }})</span>
        </div>

        {{-- Tombol Aksi menggunakan Mary UI --}}
        <div class="flex items-center space-x-1">
            @if($level < 2) {{-- Batas kedalaman 3 level (0, 1, 2) --}}
            <x-mary-button wire:click="create({{ $group->id }})" icon="o-plus" class="btn-xs btn-success btn-ghost" tooltip-left="Tambah Sub" />
            @endif
            <x-mary-button wire:click="edit({{ $group->id }})" icon="o-pencil" class="btn-xs btn-warning btn-ghost" tooltip-left="Edit" />
            <x-mary-button wire:click="delete({{ $group->id }})" wire:confirm="Yakin menghapus '{{ $group->nama_kelompok }}' dan semua sub-kelompoknya?" icon="o-trash" class="btn-xs btn-error btn-ghost" tooltip-left="Hapus" />
        </div>
    </div>

    {{-- Panggil komponen ini lagi untuk setiap turunan (children) --}}
    @if($group->childrenRecursive->isNotEmpty())
        <div class="border-l-2 border-base-300">
            @foreach($group->childrenRecursive as $child)
                @include('livewire.prodi.partials.student-group-item', ['group' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>
