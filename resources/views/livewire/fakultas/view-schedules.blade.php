<div>

@if($schedules->isNotEmpty())
    {{-- HANYA LOOPING `$schedules` TANPA `->groupBy('day.name')` --}}
    @foreach($schedules as $day => $daySchedules)
        <x-mary-card :title="$day" class="shadow-sm mb-6">
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
                    @foreach($daySchedules as $schedule)
                        <tr>
                            <td class="font-mono text-xs">{{ \Carbon\Carbon::parse($schedule->timeSlot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->timeSlot->end_time)->format('H:i') }}</td>
                            <td>
                                <div class="font-bold">{{ $schedule->activity->subject->nama_matkul ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $schedule->activity->subject->kode_matkul ?? '' }}</div>
                            </td>
                            <td>
                                {!! $schedule->activity->teachers->pluck('full_name')->implode('<br>') !!}
                            </td>
                            <td>
                                @forelse($schedule->activity->studentGroups as $studentGroup)
                                    <x-mary-badge :value="$studentGroup->nama_kelompok" class="badge-neutral mr-1 mb-1" />
                                @empty
                                    <x-mary-badge value="N/A (Kelompok tidak ditemukan)" class="badge-error" />
                                @endforelse
                            </td>
                            <td>{{ $schedule->room->kode_ruangan ?? '-' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </x-mary-card>
    @endforeach

    {{-- Pagination tidak bisa digunakan dengan logika ini, jadi bisa di-nonaktifkan atau dihapus --}}
    {{-- <div class="mt-6">
        {{ $schedules->links() }}
    </div> --}}
@else
    <x-mary-alert title="Belum Ada Jadwal" description="Jadwal untuk prodi ini belum tersedia atau belum digenerate." icon="o-exclamation-triangle" class="alert-warning mt-6" />
@endif

</div>
