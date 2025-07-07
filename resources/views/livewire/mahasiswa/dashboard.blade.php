<div>
    <div class="p-6 lg:p-8">
        {{-- Header Sambutan --}}
        <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                Jadwal Perkuliahan Anda
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Berikut adalah jadwal untuk kelompok: <strong>{{ auth()->user()->studentGroup->nama_kelompok ?? 'Belum terdaftar di kelompok manapun' }}</strong>
            </p>
        </div>

        {{-- Tampilan Jadwal --}}
        <div class="mt-6 space-y-6">
            @forelse($schedules as $day => $daySchedules)
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border dark:border-gray-700">
                    <h3 class="text-xl font-bold text-indigo-600 dark:text-indigo-400 mb-4">{{ $day }}</h3>
                    <div class="space-y-4">
                        @foreach($daySchedules->sortBy('timeSlot.start_time') as $schedule)
                            <div class="p-4 border-l-4 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 rounded-r-lg">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-bold text-gray-900 dark:text-white">{{ $schedule->subject->nama_matkul ?? 'N/A' }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $schedule->teacher->nama_dosen ?? 'N/A' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-800 dark:text-gray-300">
                                            {{ date('H:i', strtotime($schedule->timeSlot->start_time)) }} - {{ date('H:i', strtotime($schedule->timeSlot->end_time)) }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $schedule->room->kode_ruangan ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border dark:border-gray-700 text-center">
                    <p class="text-gray-500 dark:text-gray-400">Jadwal Anda belum tersedia atau belum di-generate.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
