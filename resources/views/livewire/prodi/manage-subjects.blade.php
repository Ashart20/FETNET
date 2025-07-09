<div>

    <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">

        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Manajemen Data Mata Kuliah</h1>

        @if (session()->has('message'))
            <div class="bg-green-100 dark:bg-green-900/50 border-l-4 border-green-500 dark:border-green-600 text-green-700 dark:text-green-300 p-4 my-4 rounded-md" role="alert">
                <p>{{ session('message') }}</p>
            </div>
        @endif

        <button wire:click="create()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded my-3">
            Tambah Mata Kuliah Baru
        </button>
        <button wire:click="deleteAllSubjects"
                wire:confirm="PERHATIAN! Anda yakin ingin menghapus SEMUA mata kuliah untuk prodi ini? Aksi ini tidak bisa dibatalkan."
                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
            Hapus Semua
        </button>
        @if($isModalOpen)
            @include('livewire.prodi.subject-modal')
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mt-4">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama Mata Kuliah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">SKS</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($subjects as $index => $subject)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200">{{ $subjects->firstItem() + $index }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200">{{ $subject->nama_matkul }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200">{{ $subject->kode_matkul }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-200">{{ $subject->sks }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button wire:click="edit({{ $subject->id }})" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1 px-2 rounded text-xs">Edit</button>
                            <button wire:click="delete({{ $subject->id }})" wire:confirm="Anda yakin ingin menghapus data ini?"
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-xs ml-2">
                                Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            Belum ada data mata kuliah.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mb-6 p-4 bg-white dark:bg-gray-800/50 shadow-sm rounded-xl border dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-white">Impor Data dari Excel</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Unggah file .xlsx atau .xls dengan kolom: `nama_matkul`, `kode_matkul`, `sks`.</p>

            @if (session()->has('error'))
                <div class="bg-red-100 whitespace-pre-wrap dark:bg-red-900/50 border-l-4 border-red-500 text-red-700 dark:text-red-300 p-4 my-4 rounded-md" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="mt-4">
                {{-- Input ini sekarang langsung memicu proses impor secara otomatis --}}
                <input type="file" wire:model="file" id="file-upload" class="block w-full text-sm text-gray-500
            file:mr-4 file:py-2 file:px-4
            file:rounded-md file:border-0
            file:text-sm file:font-semibold
            file:bg-blue-50 dark:file:bg-blue-900/50
            file:text-blue-700 dark:file:text-blue-300
            hover:file:bg-blue-100 dark:hover:file:bg-blue-900
        "/>

                {{-- Indikator loading saat file sedang di-upload dan diproses --}}
                <div wire:loading wire:target="file" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Mengunggah dan memproses file...
                </div>

                @error('file') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

                <button type="submit" wire:loading.attr="disabled" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50">
                    <span wire:loading.remove wire:target="importExcel">Impor</span>
                    <span wire:loading wire:target="importExcel">Mengimpor...</span>
                </button>
            </form>
        </div>
        <div class="mt-4">
            {{ $subjects->links() }}
        </div>
    </div>
</div>
