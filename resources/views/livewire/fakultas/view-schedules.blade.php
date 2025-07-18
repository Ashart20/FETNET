<div>
    {{-- Dropdown filter prodi Anda --}}
    <div class="my-4">
        <x-mary-select label="Pilih Program Studi" wire:model.live="selectedProdiId" :options="$prodis" option-value="id" option-label="nama_prodi" placeholder="-- Pilih Prodi --" />
    </div>

    @if($schedules->isNotEmpty())
        <div class="space-y-6">
            {{-- Perulangan utama berdasarkan HARI --}}
            @foreach($schedules as $day => $daySchedules)
                <x-mary-card :title="$day" class="shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th>Jam</th>
                                <th>Mata Kuliah</th>
                                <th>Dosen</th>
                                <th>Kelas</th>
                                <th>Ruangan</th>
                            </tr>
                            </thead>
                            <tbody>
                            {{-- Perulangan kedua untuk setiap jadwal yang SUDAH DIGABUNG --}}
                            @foreach($daySchedules->sortBy('timeSlot.start_time') as $schedule)
                                <tr>
                                    {{-- Menampilkan jam mulai dari blok pertama dan jam selesai dari blok terakhir --}}
                                    <td class="font-mono text-xs whitespace-nowrap">{{ \Carbon\Carbon::parse($schedule->timeSlot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->timeSlot->end_time)->format('H:i') }}</td>
                                    <td>
                                        <div class="font-bold">{{ $schedule->activity->subject->nama_matkul ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500">{{ $schedule->activity->subject->kode_matkul ?? '' }}</div>
                                    </td>
                                    <td>
                                        {{-- Mengambil nama dosen dari relasi --}}
                                        @foreach($schedule->activity->teachers as $teacher)
                                            {{ $teacher->full_name }}@if(!$loop->last),<br>@endif
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($schedule->activity->studentGroups as $studentGroup)
                                                <x-mary-badge :value="$studentGroup->nama_kelompok" class="badge-neutral" />
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>{{ $schedule->room->nama_ruangan ?? '-' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-mary-card>
            @endforeach
        </div>
    @else
        <div class="p-4 text-center text-gray-500">
            <p>Tidak ada jadwal yang tersedia untuk program studi ini.</p>
        </div>
    @endif
</div>
