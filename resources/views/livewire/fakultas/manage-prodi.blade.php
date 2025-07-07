<div>
    <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">

        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Manajemen Program Studi</h1>

        @if (session()->has('message'))
            <div class="bg-green-100 dark:bg-green-900/50 border-l-4 border-green-500 dark:border-green-600 text-green-700 dark:text-green-300 p-4 my-4 rounded-md" role="alert">
                <p>{{ session('message') }}</p>
            </div>
        @endif

        <button wire:click="create()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded my-3">
            Tambah Prodi Baru
        </button>

        @if($isModalOpen)
            @include('livewire.fakultas.prodi-modal')
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mt-4">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama Prodi</th>
                    {{-- PERBAIKAN: Ganti header kolom --}}
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($prodis as $index => $prodi)
                    <tr wire:key="{{ $prodi->id }}">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200">{{ $prodis->firstItem() + $index }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200">{{ $prodi->nama_prodi }}</td>
                        {{-- PERBAIKAN: Tampilkan kolom 'kode' --}}
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200">{{ $prodi->kode }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button wire:click="edit({{ $prodi->id }})" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1 px-2 rounded text-xs">Edit</button>
                            {{-- PERBAIKAN: Gunakan wire:confirm untuk konfirmasi hapus --}}
                            <button wire:click="delete({{ $prodi->id }})" wire:confirm="Anda yakin ingin menghapus prodi ini?"
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-xs ml-2">
                                Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            Belum ada data program studi.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $prodis->links() }}
        </div>
    </div>
</div>
