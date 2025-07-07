<div class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-md">
        <div class="flex justify-between items-center pb-3 border-b dark:border-gray-700">
            {{-- PERBAIKAN: Gunakan $teacherId untuk mengecek mode --}}
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $teacherId ? 'Edit Data Dosen' : 'Tambah Dosen Baru' }}</p>
            <button wire:click="closeModal()" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white">&times;</button>
        </div>

        <form wire:submit.prevent="store" class="pt-4">
            <div class="mb-4">
                {{-- PERBAIKAN: Sesuaikan 'for' dengan 'id' input --}}
                <label for="nama_dosen" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Nama Dosen:</label>
                <input type="text" id="nama_dosen" wire:model="nama_dosen" placeholder="Contoh: Dr. Andi, M.Kom."
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600">
                @error('nama_dosen') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                {{-- PERBAIKAN: Sesuaikan 'for' dengan 'id' input --}}
                <label for="kode_dosen" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Kode Dosen:</label>
                <input type="text" id="kode_dosen" wire:model="kode_dosen" placeholder="Contoh: AND"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600">
                @error('kode_dosen') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end pt-4">
                <button type="button" wire:click="closeModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">Batal</button>
                <button type="submit" wire:loading.attr="disabled" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50">
                    <span wire:loading.remove>
                        {{ $teacherId ? 'Update Data' : 'Simpan' }}
                    </span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>
