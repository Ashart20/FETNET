<div>
    {{-- Notifikasi Konflik --}}
    <div x-data="{ show: @entangle('showConflictNotification'), message: '' }"
         x-init="
            Livewire.on('show-conflict-notification', (data) => {
                message = 'Ditemukan ' + data.count + ' konflik jadwal!';
                show = true;
            });
            // Pastikan notifikasi konflik juga bisa ditutup secara manual
            Livewire.on('clearConflictAlert', () => {
                show = false;
            });
         "
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-500"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
         role="alert"
    >
        <strong class="font-bold">Perhatian!</strong>
        <span class="block sm:inline" x-text="message"></span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg wire:click="clearConflictAlert" class="fill-current h-6 w-6 text-red-500 cursor-pointer" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.107l-2.651 3.742a1.2 1.2 0 1 1-1.697-1.697l3.742-2.651-3.742-2.651a1.2 1.2 0 1 1 1.697-1.697l2.651 3.742 2.651-3.742a1.2 1.2 0 1 1 1.697 1.697l-3.742 2.651 3.742 2.651a1.2 1.2 0 0 1 0 1.697z"/></svg>
        </span>
    </div>

    {{-- Detail Konflik --}}
    @if (!empty($this->conflicts))
        <h3 class="text-lg font-semibold text-red-600 mb-3">Detail Konflik Jadwal:</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300">
                <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Jenis Konflik</th>
                    <th class="py-2 px-4 border-b">Sumber Daya</th>
                    <th class="py-2 px-4 border-b">Waktu</th>
                    <th class="py-2 px-4 border-b">Sesi Terlibat</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($this->conflicts as $conflict)
                    <tr class="bg-red-50">
                        <td class="py-2 px-4 border-b text-red-700">{{ $conflict['type'] }}</td>
                        <td class="py-2 px-4 border-b">{{ $conflict['resource'] }}</td>
                        <td class="py-2 px-4 border-b">{{ $conflict['time'] }}</td>
                        <td class="py-2 px-4 border-b">
                            <ul>
                                @foreach ($conflict['sessions'] as $session)
                                    <li>- {{ $session }}</li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Notifikasi "Tidak Ada Konflik!" --}}
    <div x-data="{ show: @entangle('showCleanNotification') }"
         x-init="setTimeout(() => show = false, 5000)"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-500"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
         role="alert"
    >
        <strong class="font-bold">Tidak Ada Konflik!</strong>
        <span class="block sm:inline">Jadwal Anda bersih dari bentrokan.</span>
    </div>
</div>
