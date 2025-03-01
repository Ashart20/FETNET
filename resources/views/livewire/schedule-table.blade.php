<div wire:poll.5s>
    <h1 class="text-xl font-bold mb-4">Jadwal Perkuliahan</h1>

    <table class="w-full border-collapse border border-gray-300">
        <thead>
        <tr class="bg-gray-200">
            <th class="border p-2">Mata Kuliah</th>
            <th class="border p-2">Dosen</th>
            <th class="border p-2">Ruangan</th>
            <th class="border p-2">Waktu</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($schedules as $schedule)
            <tr>
                <td class="border p-2">{{ $schedule->course }}</td>
                <td class="border p-2">{{ $schedule->lecturer }}</td>
                <td class="border p-2">{{ $schedule->room }}</td>
                <td class="border p-2">{{ $schedule->time_slot }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
