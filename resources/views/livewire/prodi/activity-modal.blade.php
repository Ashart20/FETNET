<div class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-md">
        <div class="flex justify-between items-center pb-3 border-b dark:border-gray-700">
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $activityId ? 'Edit Aktivitas' : 'Tambah Aktivitas Baru' }}</p>
            <button wire:click="closeModal()" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white text-2xl font-bold focus:outline-none">&times;</button>
        </div>

        <form wire:submit.prevent="store" class="pt-4 space-y-4">
            {{-- Dropdown Dosen (sekarang bisa multi-select) --}}
            <div class="mb-4">
                <label for="teacher_ids" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Pilih Dosen (Bisa lebih dari satu):</label>
                <div wire:ignore>
                    <select id="select-teachers" wire:model="teacher_ids" multiple
                            placeholder="Ketik untuk mencari dosen..." autocomplete="off"
                            class="tom-select-class"> {{-- Tambahkan kelas placeholder jika perlu styling khusus --}}
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->nama_dosen }}</option>
                        @endforeach
                    </select>
                </div>
                @error('teacher_ids') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Dropdown Mata Kuliah --}}
            <div>
                <label for="subject_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Mata Kuliah:</label>
                <select id="subject_id" wire:model.defer="subject_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Mata Kuliah --</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->nama_matkul }}</option>
                    @endforeach
                </select>
                @error('subject_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Dropdown Kelompok Mahasiswa --}}
            <div>
                <label for="student_group_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Kelompok Mahasiswa:</label>
                <select id="student_group_id" wire:model.defer="student_group_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Kelompok --</option>
                    @foreach($studentGroups as $group)
                        <option value="{{ $group->id }}">{{ $group->nama_kelompok }}</option>
                    @endforeach
                </select>
                @error('student_group_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Dropdown Activity Tag (Opsional) --}}
            <div>
                <label for="activity_tag_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Pilih Tag (Opsional):</label>
                <select id="activity_tag_id" wire:model.defer="activity_tag_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Tanpa Tag --</option>
                    @foreach($activityTags as $tag)
                        <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                    @endforeach
                </select>
                @error('activity_tag_id') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>



            <div class="flex justify-end pt-4 space-x-2">
                <button type="button" wire:click="closeModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold text-xs uppercase rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 transition">Batal</button>
                <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 bg-blue-600 text-white font-semibold text-xs uppercase rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition">
                    <span wire:loading.remove>{{ $activityId  ? 'Update' : 'Simpan' }}</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>
