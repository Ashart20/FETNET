<div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-md">
        <div class="flex justify-between items-center pb-3 border-b dark:border-gray-700">
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $subjectId ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah' }}</p>
            <button wire:click="closeModal()" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white">&times;</button>
        </div>

        <form wire:submit.prevent="store" class="pt-4">
            <div class="mb-4">
                <label for="nama_matkul" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Nama Mata Kuliah:</label>
                <input type="text" id="nama_matkul" wire:model.defer="nama_matkul"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600">
                @error('nama_matkul') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="kode_matkul" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Kode Mata Kuliah:</label>
                <input type="text" id="kode_matkul" wire:model.defer="kode_matkul"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600">
                @error('kode_matkul') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="sks" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Jumlah SKS:</label>
                <input type="number" id="sks" wire:model.defer="sks"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600">
                @error('sks') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end pt-4">
                <button type="button" wire:click="closeModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">Batal</button>
                <button type="submit" wire:loading.attr="disabled" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50">
                    <span wire:loading.remove>{{ $subjectId ? 'Update' : 'Simpan' }}</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>
