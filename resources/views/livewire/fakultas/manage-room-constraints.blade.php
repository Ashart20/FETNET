<div>
    <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Manajemen Batasan Waktu Ruangan</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Pilih ruangan, lalu klik pada slot waktu untuk menandainya sebagai 'tidak tersedia' (merah).</p>

        @if (session()->has('message'))
            <div class="bg-green-100 dark:bg-green-900/50 border-l-4 border-green-500 text-green-700 dark:text-green-300 p-4 my-4 rounded-md" role="alert">
                <p>{{ session('message') }}</p>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="bg-red-100 dark:bg-red-900/50 border-l-4 border-red-500 text-red-700 dark:text-red-300 p-4 my-4 rounded-md" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        {{-- Dropdown untuk memilih ruangan --}}
        <div class="my-4">
            <label for="room" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Ruangan:</label>
            <select wire:model.live="selectedRoomId" id="room" class="mt-1 block w-full md:w-1/3 pl-3 pr-10 py-2 text-base border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="">-- Pilih Ruangan --</option>
                @foreach($rooms as $room)
                    <option value="{{ $room->id }}">{{ $room->kode_ruangan }} - {{ $room->nama_ruangan }}</option>
                @endforeach
            </select>
        </div>

        {{--
            PERBAIKAN:
            Logika `wire:loading` dihapus dari pembungkus tabel untuk memastikan tabel selalu ditampilkan.
        --}}
        <div class="overflow-x-auto">
            <div wire:loading.class="opacity-50" wire:target="selectedRoomId">
                <table class="min-w-full border-collapse">
                    <thead>
                    <tr>
                        <th class="p-2 border dark:border-gray-600 bg-gray-100 dark:bg-gray-900/50 text-gray-600 dark:text-gray-300 w-32">Waktu</th>
                        @foreach($days as $day)
                            <th wire:key="day-{{ $day->id }}" class="p-2 border dark:border-gray-600 bg-gray-100 dark:bg-gray-900/50 text-gray-600 dark:text-gray-300">{{ $day->name }}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($timeSlots as $slot)
                        <tr wire:key="slot-{{ $slot->id }}" class="text-center">
                            <td class="p-2 border dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-300 text-xs">
                                {{ date('H:i', strtotime($slot->start_time)) }} - {{ date('H:i', strtotime($slot->end_time)) }}
                            </td>
                            @foreach($days as $day)
                                @php
                                    $key = $day->id . '-' . $slot->id;
                                    $isConstrained = isset($constraints[$key]);
                                @endphp

                                <td wire:key="cell-{{ $day->id }}-{{ $slot->id }}"
                                    wire:click="{{ $selectedRoomId ? 'toggleConstraint('.$day->id.', '.$slot->id.')' : '' }}"
                                    class="p-2 border dark:border-gray-600 transition-colors
                                            @if($selectedRoomId)
                                                {{ $isConstrained ? 'bg-red-200 dark:bg-red-800/60 hover:bg-red-300 dark:hover:bg-red-700' : 'bg-green-200 dark:bg-green-800/30 hover:bg-green-300 dark:hover:bg-green-700' }} cursor-pointer
                                            @else
                                                bg-gray-100 dark:bg-gray-800 cursor-not-allowed
                                            @endif
                                        ">
                                    <div wire:loading wire:target="toggleConstraint({{ $day->id }}, {{ $slot->id }})">
                                        <svg class="animate-spin h-5 w-5 mx-auto text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($days) + 1 }}" class="p-4 text-center text-gray-500 dark:text-gray-400">
                                Tidak ada data slot waktu di database. Mohon jalankan Seeder.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Legenda --}}
        <div class="mt-4 text-sm text-gray-600 dark:text-gray-400 flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
            <div class="flex items-center"><div class="w-4 h-4 mr-2 bg-green-200 dark:bg-green-800/30 border dark:border-gray-600"></div> Waktu Tersedia</div>
            <div class="flex items-center"><div class="w-4 h-4 mr-2 bg-red-200 dark:bg-red-800/60 border dark:border-gray-600"></div> Waktu Tidak Tersedia</div>
            <div class="flex items-center"><div class="w-4 h-4 mr-2 bg-gray-100 dark:bg-gray-800 border dark:border-gray-600"></div> Pilih ruangan untuk memulai</div>
        </div>
    </div>
</div>
