<div class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-md">
        <div class="flex justify-between items-center pb-3 border-b dark:border-gray-600">
            {{-- PERBAIKAN: Tambahkan dark mode pada teks --}}
            <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $prodiId ? 'Edit Prodi' : 'Tambah Prodi Baru' }}</p>
            <button wire:click="closeModal()" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 text-2xl">&times;</button>
        </div>

        {{-- PERBAIKAN: Tambahkan dark mode pada label --}}
        <form wire:submit.prevent="store" class="pt-4">
            <div class="mb-4">
                <label for="nama_prodi" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Nama Prodi:</label>
                {{-- PERBAIKAN: Ganti 'wire:model.defer' ke 'wire:model' untuk validasi real-time --}}
                <input type="text" id="nama_prodi" wire:model="nama_prodi" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('nama_prodi') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                {{-- PERBAIKAN: Ganti 'for' dan label --}}
                <label for="kode" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Kode Prodi:</label>
                {{-- PERBAIKAN: Ganti 'id' dan 'wire:model' --}}
                <input type="text" id="kode" wire:model="kode" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('kode') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end pt-4">
                <button type="button" wire:click="closeModal()" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded mr-2">
                    Batal
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    {{ $prodiId ? 'Update' : 'Simpan' }}
                </button>
            </div>
        </form>
    </div>
</div>
