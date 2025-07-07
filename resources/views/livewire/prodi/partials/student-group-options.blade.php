{{-- Menambahkan spasi (indent) sesuai level kedalaman --}}
<option value="{{ $group->id }}" class="font-semibold">
    {{ str_repeat('--', $level ?? 0) }} {{ $group->nama_kelompok }}
</option>

{{-- Jika grup ini punya turunan, panggil lagi file ini untuk setiap turunannya --}}
@if($group->childrenRecursive->isNotEmpty())
    @foreach($group->childrenRecursive as $child)
        @include('livewire.prodi.partials.student-group-options', ['group' => $child, 'level' => ($level ?? 0) + 1])
    @endforeach
@endif
