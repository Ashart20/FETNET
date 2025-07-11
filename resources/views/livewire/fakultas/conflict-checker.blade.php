<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Pengecekan Jadwal Dosen') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="mb-4 text-lg font-medium">Periksa Jadwal Dosen</h3>
                    <p class="mb-4 text-sm text-gray-600">Pilih seorang dosen untuk melihat jadwal lengkap mereka secara otomatis.</p>

                    <div class="mb-4">
                        <label for="teacher_id" class="block mb-2 text-sm font-bold text-gray-700">Pilih Dosen:</label>
                        <select wire:model.live="selectedTeacherId" id="teacher_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Pilih Dosen --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->nama_dosen }} ({{ $teacher->kode_dosen }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div wire:loading class="mt-8">
                <p class="text-gray-600">Memuat jadwal...</p>
            </div>

            <div wire:loading.remove>
                @if ($schedules)
                    <div class="mt-8">
                        <h3 class="mb-4 text-xl font-semibold">Hasil untuk: {{ optional($selectedTeacher)->nama_dosen }}</h3>

                        @if($hardConflicts->isNotEmpty())
                            <div class="p-4 mb-4 text-red-800 bg-red-100 border border-red-400 rounded-md" role="alert">
                                <p class="font-bold">Peringatan: Ditemukan Konflik Jadwal Keras!</p>
                                <p>Dosen ini dijadwalkan di lebih dari satu tempat pada waktu yang sama.</p>
                            </div>
                        @endif

                        <div class="overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <table class="min-w-full bg-white divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Hari</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Waktu</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Mata Kuliah</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Prodi</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Ruangan</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Kelompok Mahasiswa</th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($schedules as $schedule)
                                        <tr @if($hardConflicts->has($schedule->day_id . '-' . $schedule->time_slot_id)) class="bg-red-50" @endif>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ optional($schedule->day)->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $schedule->timeSlot ? date('H:i', strtotime($schedule->timeSlot->start_time)) . ' - ' . date('H:i', strtotime($schedule->timeSlot->end_time)) : 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ optional($schedule->activity->subject)->nama_matkul ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full">
                                                    {{ optional($schedule->prodi)->kode ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ optional($schedule->room)->nama_ruangan ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ optional($schedule->activity)->studentGroups->pluck('nama_kelompok')->implode(', ') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 whitespace-nowrap">Tidak ada jadwal yang ditemukan untuk dosen ini.</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
