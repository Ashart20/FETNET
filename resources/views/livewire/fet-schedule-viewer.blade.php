<div class="p-4">

    {{-- Filter & Tombol --}}
    <div class="flex flex-wrap items-end gap-4 mb-6">
        @foreach ([
            ['label' => 'Hari', 'model' => 'filterHari', 'list' => $daftarHari],
            ['label' => 'Kelas', 'model' => 'filterKelas', 'list' => $daftarKelas],
            ['label' => 'Mata Kuliah', 'model' => 'filterMatkul', 'list' => $daftarMatkul],
            ['label' => 'Ruangan', 'model' => 'filterRuangan', 'list' => $daftarRuangan],
            ['label' => 'Dosen', 'model' => 'filterDosen', 'list' => $daftarDosen],
        ] as $filter)
            <div>
                <label class="block text-sm font-medium mb-1">{{ $filter['label'] }}</label>
                <select wire:model.defer="{{ $filter['model'] }}"
                        class="rounded-md border-gray-300 text-sm shadow-sm">
                    <option value="">-- Semua --</option>
                    @foreach ($filter['list'] as $item)
                        <option value="{{ $item }}">{{ $item }}</option>
                    @endforeach
                </select>
            </div>
        @endforeach

        {{-- Tombol --}}
        <div class="flex items-center gap-2">
            <button wire:click="applyFilter"
                    class="text-sm px-3 py-1 border border-gray-300 rounded hover:bg-gray-100 transition">
                Terapkan
            </button>

            <button wire:click="resetFilter"
                    title="Reset Filter"
                    class="text-gray-500 hover:text-red-500 text-lg transition">
                🔄
            </button>
        </div>
    </div>

    {{-- Tabel --}}
    <table class="min-w-full text-sm bg-white border rounded">
        <thead class="bg-gray-100">
        <tr>
            <th class="px-3 py-2">Kode MK</th>
            <th class="px-3 py-2">Mata Kuliah</th>
            <th class="px-3 py-2">SKS</th>
            <th class="px-3 py-2">Kode Dosen</th>
            <th class="px-3 py-2">Nama Dosen</th>
            <th class="px-3 py-2">Kelas</th>
            <th class="px-3 py-2">Hari</th>
            <th class="px-3 py-2">Jam</th>
            <th class="px-3 py-2">Ruangan</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($jadwal as $item)
            <tr class="border-t">
                <td class="px-3 py-2">{{ $item->kode_mk ?? '-' }}</td>
                <td class="px-3 py-2">{{ $item->subject ?? '-' }}</td>
                <td class="px-3 py-2">{{ $item->sks ?? '-' }}</td>
                <td class="px-3 py-2">{{ $item->kode_dosen ?? '-' }}</td>
                <td class="px-3 py-2">{{ $item->teacher ?? '-' }}</td>
                <td class="px-3 py-2">{{ $item->kelas ?? '-' }}</td>
                <td class="px-3 py-2">{{ ucfirst(optional($item->timeSlot)->day ?? '-') }}</td>
                <td class="px-3 py-2">
                    {{ optional($item->timeSlot)?->start_time ? \Carbon\Carbon::parse($item->timeSlot->start_time)->format('H:i') : '-' }}
                    -
                    {{ optional($item->timeSlot)?->end_time ? \Carbon\Carbon::parse($item->timeSlot->end_time)->format('H:i') : '-' }}
                </td>
                <td class="px-3 py-2">{{ optional($item->room)->name ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center py-4 text-gray-500">Tidak ada data ditemukan.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $jadwal->links() }}
    </div>
</div>
