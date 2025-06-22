<div class="p-4">
    @livewire('schedule-conflict-detector')

    <hr class="my-6">

    {{-- Filter & Tombol --}}
    <div class="flex flex-wrap items-end gap-4 mb-6 p-4 bg-white shadow-sm rounded-lg">
        {{-- Grup Filter --}}
        <div class="flex flex-wrap gap-x-6 gap-y-4">
            {{-- Loop sekarang mengiterasi properti filtersConfig dari komponen --}}
            @foreach ($this->filtersConfig as $filter)
                <div class="flex flex-col">
                    <label for="{{ $filter['model'] }}" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $filter['label'] }}
                    </label>
                    <select wire:model.defer="{{ $filter['model'] }}"
                            id="{{ $filter['model'] }}"
                            class="form-select block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm">
                        <option value="">-- Semua --</option>
                        {{-- Akses daftar item melalui $filter['data_list'] --}}
                        @foreach ($filter['data_list'] as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
            @endforeach
        </div>

        {{-- Tombol --}}
        <div class="flex items-end gap-2">
            <button wire:click="applyFilter"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01.293.707V19a1 1 0 01-1 1H4a1 1 0 01-1-1V6.586a1 1 0 01.293-.707L3 4zm7 10a2 2 0 100-4 2 2 0 000 4z"></path></svg>
                Terapkan
            </button>

            <button wire:click="resetFilter"
                    title="Reset Filter"
                    class="inline-flex items-center p-2 border border-gray-300 rounded-md shadow-sm text-gray-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition ease-in-out duration-150">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004 13V4m7 7v9h1.347a.75.75 0 00.582-.294l-.134-.132a.75.75 0 00-.582-.294H11V11m-4 0h4m-4 4h4m-4 4h4"></path></svg>
            </button>
        </div>
    </div>

    <div class="overflow-x-auto rounded-lg shadow-md mt-6">
        <table class="min-w-full text-sm bg-white">
            <thead class="bg-gray-100 border-b border-gray-200">
            <tr>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Kode MK</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Mata Kuliah</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">SKS</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Kode Dosen</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Nama Dosen</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Kelas</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Hari</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Jam</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Ruangan</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
            @forelse ($jadwal as $item)
                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                    <td class="px-4 py-3 whitespace-nowrap">{{ $item->kode_mk ?? '-' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $item->subject ?? '-' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $item->sks ?? '-' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $item->kode_dosen ?? '-' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $item->teacher ?? '-' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $item->kelas ?? '-' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ ucfirst(optional($item->timeSlot)->day ?? '-') }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        {{ optional($item->timeSlot)?->start_time ? \Carbon\Carbon::parse($item->timeSlot->start_time)->format('H:i') : '-' }}
                        -
                        {{ optional($item->timeSlot)?->end_time ? \Carbon\Carbon::parse($item->timeSlot->end_time)->format('H:i') : '-' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ optional($item->room)->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-gray-500">Tidak ada data ditemukan.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{-- Pagination --}}
    <div class="mt-4 p-4 bg-white rounded-lg shadow-sm flex justify-center">
        {{ $jadwal->links() }}
    </div>
</div>
