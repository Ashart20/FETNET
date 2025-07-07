<div>
    <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Manajemen Master Ruangan</h1>

        @if (session()->has('message'))
            <div class="bg-green-100 dark:bg-green-900/50 border-l-4 border-green-500 text-green-700 dark:text-green-300 p-4 my-4 rounded-md" role="alert">
                <p>{{ session('message') }}</p>
            </div>
        @endif

        <button wire:click="create()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded my-3">
            Tambah Ruangan Baru
        </button>

        @if($isModalOpen)
            @include('livewire.fakultas.room-modal')
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mt-4">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama Ruangan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Gedung</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Lantai</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kapasitas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($rooms as $room)
                    {{-- PERBAIKAN: Menambahkan wire:key untuk optimasi --}}
                    <tr wire:key="{{ $room->id }}">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200">{{ $room->nama_ruangan }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200">{{ $room->kode_ruangan }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200">{{ $room->building->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200">{{ $room->tipe }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200">{{ $room->lantai }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200">{{ $room->kapasitas }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button wire:click="edit({{ $room->id }})" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1 px-2 rounded text-xs">Edit</button>
                            {{-- PERBAIKAN: Menggunakan wire:confirm untuk konfirmasi hapus --}}
                            <button wire:click="delete({{ $room->id }})" wire:confirm="Anda yakin ingin menghapus ruangan ini?"
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-xs ml-2">
                                Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            Belum ada data ruangan.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $rooms->links() }}
        </div>
    </div>
</div>
