<div class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-md">
        <div class="flex justify-between items-center pb-3 border-b dark:border-gray-700">
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $roomId ? 'Edit Ruangan' : 'Tambah Ruangan Baru' }}</p>
            <button wire:click="closeModal()" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white text-2xl font-bold focus:outline-none">&times;</button>
        </div>

        <form wire:submit.prevent="store" class="pt-4">
            <div class="space-y-4">
                {{-- Definisikan kelas umum untuk konsistensi --}}
                @php
                    $inputClasses = "shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500";
                    $labelClasses = "block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2";
                @endphp

                <div>
                    <label for="nama_ruangan" class="{{ $labelClasses }}">Nama Ruangan:</label>
                    <input type="text" id="nama_ruangan" wire:model.defer="nama_ruangan" class="{{ $inputClasses }}">
                    @error('nama_ruangan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="kode_ruangan" class="{{ $labelClasses }}">Kode Ruangan:</label>
                    <input type="text" id="kode_ruangan" wire:model.defer="kode_ruangan" class="{{ $inputClasses }}">
                    @error('kode_ruangan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="building_id" class="{{ $labelClasses }}">Gedung:</label>
                    <select id="building_id" wire:model.defer="building_id" class="{{ $inputClasses }}">
                        <option value="">-- Pilih Gedung --</option>
                        @foreach($buildings as $building)
                            <option value="{{ $building->id }}">{{ $building->name }} ({{ $building->code }})</option>
                        @endforeach
                    </select>
                    @error('building_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- Form untuk menambah gedung baru --}}
                <div class="mt-3 p-3 border rounded-md dark:border-gray-600">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Tidak menemukan gedung? Tambah baru:</p>

                    @if (session()->has('building-message'))
                        <div class="text-green-600 dark:text-green-400 text-xs mb-2">{{ session('building-message') }}</div>
                    @endif

                    <div class="flex items-center space-x-2">
                        @php
                            $newBuildingInputClasses = "w-full text-xs border-gray-300 rounded-md py-2 px-3 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500";
                        @endphp
                        <input type="text" wire:model.defer="newBuildingName" placeholder="Nama Gedung" class="w-1/2 {{ $newBuildingInputClasses }}">
                        <input type="text" wire:model.defer="newBuildingCode" placeholder="Kode" class="w-1/3 {{ $newBuildingInputClasses }}">
                        <button type="button" wire:click="addNewBuilding" wire:loading.attr="disabled" wire:target="addNewBuilding" class="w-auto bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-3 rounded text-xs disabled:opacity-50 transition">
                            + Tambah
                        </button>
                    </div>
                    @error('newBuildingName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @error('newBuildingCode') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="tipe" class="{{ $labelClasses }}">Tipe Ruangan:</label>
                    <select id="tipe" wire:model.defer="tipe" class="{{ $inputClasses }}">
                        <option value="KELAS_TEORI">Kelas Teori</option>
                        <option value="LABORATORIUM">Laboratorium</option>
                        <option value="AUDITORIUM">Auditorium</option>
                    </select>
                </div>
                <div>
                    <label for="lantai" class="{{ $labelClasses }}">Lantai:</label>
                    <input type="text" id="lantai" wire:model.defer="lantai" class="{{ $inputClasses }}">
                    @error('lantai') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="kapasitas" class="{{ $labelClasses }}">Kapasitas:</label>
                    <input type="number" id="kapasitas" wire:model.defer="kapasitas" class="{{ $inputClasses }}">
                    @error('kapasitas') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end pt-6 space-x-2">
                <button type="button" wire:click="closeModal()" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded mr-2">Batal</button>
                <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 bg-blue-600 text-white font-semibold text-xs uppercase rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition">
                    <span wire:loading.remove>{{ $roomId ? 'Update' : 'Simpan' }}</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>
