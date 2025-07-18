@php
    /**
     * @var \Illuminate\Support\Collection $jadwal
     * @var array $daftarHari, $daftarDosen, $daftarMatkul, $daftarKelas, $daftarRuangan
     */
@endphp

<div>
    <div class="p-4 sm:p-6 lg:p-8">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Jadwal Perkuliahan</h1>

        {{-- Filters Section --}}
        @php
            $selectClasses = 'w-full text-sm rounded-lg transition bg-white border border-gray-300 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-800 dark:border-slate-700 dark:text-gray-300 dark:placeholder-gray-400';
            $labelClasses = 'block text-xs font-medium mb-1 text-gray-700 dark:text-gray-200';
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            {{-- Hari --}}
            <div>
                <label for="filterHari" class="{{ $labelClasses }}">Hari</label>
                <select wire:model.live="filterHari" id="filterHari" class="{{ $selectClasses }}">
                    <option value="">Semua Hari</option>
                    @foreach ($daftarHari as $item) <option value="{{ $item }}">{{ $item }}</option> @endforeach
                </select>
            </div>
            {{-- Dosen --}}
            <div>
                <label for="filterDosen" class="{{ $labelClasses }}">Dosen</label>
                <select wire:model.live="filterDosen" id="filterDosen" class="{{ $selectClasses }}">
                    <option value="">Semua Dosen</option>
                    @foreach ($daftarDosen as $item) <option value="{{ $item }}">{{ $item }}</option> @endforeach
                </select>
            </div>
            {{-- Mata Kuliah --}}
            <div>
                <label for="filterMatkul" class="{{ $labelClasses }}">Mata Kuliah</label>
                <select wire:model.live="filterMatkul" id="filterMatkul" class="{{ $selectClasses }}">
                    <option value="">Semua Matkul</option>
                    @foreach ($daftarMatkul as $id => $nama) <option value="{{ $id }}">{{ $nama }}</option> @endforeach
                </select>
            </div>
            {{-- Kelas --}}
            <div>
                <label for="filterKelas" class="{{ $labelClasses }}">Kelas</label>
                <select wire:model.live="filterKelas" id="filterKelas" class="{{ $selectClasses }}">
                    <option value="">Semua Kelas</option>
                    @foreach ($daftarKelas as $item) <option value="{{ $item }}">{{ $item }}</option> @endforeach
                </select>
            </div>
            {{-- Ruangan & Reset --}}
            <div class="flex items-end gap-x-2">
                <div class="flex-grow">
                    <label for="filterRuangan" class="{{ $labelClasses }}">Ruangan</label>
                    <select wire:model.live="filterRuangan" id="filterRuangan" class="{{ $selectClasses }}">
                        <option value="">Semua Ruangan</option>
                        @foreach ($daftarRuangan as $item) <option value="{{ $item }}">{{ $item }}</option> @endforeach
                    </select>
                </div>
                <div class="flex-shrink-0">
                    <button wire:click="resetFilters" title="Reset Semua Filter" class="{{ $selectClasses }} h-full inline-flex items-center justify-center px-3 hover:bg-slate-700">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0011.664 0l3.18-3.185m-4.992-2.686a3.75 3.75 0 01-5.304 0L9 15.121m-2.12-2.828a3.75 3.75 0 015.304 0L15 9.348" /></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Schedule Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        @forelse ($jadwal as $hari => $jadwalHarian)
            <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $hari }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-900/50 text-xs text-gray-500 dark:text-gray-300 uppercase">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-medium">Jam</th>
                        <th scope="col" class="px-6 py-3 font-medium">Mata Kuliah</th>
                        <th scope="col" class="px-6 py-3 font-medium">Dosen</th>
                        <th scope="col" class="px-6 py-3 font-medium">Kelas</th>
                        <th scope="col" class="px-6 py-3 font-medium">Ruangan</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($jadwalHarian->sortBy('timeSlot.start_time') as $item)
                        <tr wire:key="schedule-{{ $item->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400 font-mono">{{ \Carbon\Carbon::parse(optional($item->timeSlot)->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse(optional($item->timeSlot)->end_time)->format('H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white font-medium">
                                {{ $item->activity->subject->nama_matkul ?? 'N/A' }}
                                <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $item->activity->subject->kode_matkul ?? '' }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-900 dark:text-white">
                                {!! $item->activity->teachers->pluck('full_name')->implode('<br>') !!}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400">
                                @forelse($item->activity->studentGroups as $studentGroup)
                                    <x-mary-badge :value="$studentGroup->nama_kelompok" class="badge-neutral mr-1 mb-1" />
                                @empty
                                    -
                                @endforelse
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">{{ $item->room->nama_ruangan ?? '-' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div class="text-center py-10 text-gray-500 dark:text-gray-400">
                <p>Tidak ada data jadwal ditemukan yang sesuai dengan filter.</p>
            </div>
        @endforelse
    </div>
</div>
