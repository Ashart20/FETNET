<div class="p-4">
    <h2 class="text-xl font-semibold mb-4">📋 Hasil Generate Jadwal FET</h2>

    <div class="overflow-auto rounded shadow-sm border border-gray-200">
        <table class="min-w-full bg-white text-sm">
            <thead class="bg-blue-100 text-gray-700 text-xs uppercase text-left">
            <tr>
                <th class="px-4 py-2">No</th>
                <th class="px-4 py-2">Kode</th>
                <th class="px-4 py-2">Mata Kuliah</th>
                <th class="px-4 py-2">SKS</th>
                <th class="px-4 py-2">Kode Dosen</th>
                <th class="px-4 py-2">Nama Dosen</th>
                <th class="px-4 py-2">Kelas / Hari / Jam / Ruang / Peserta</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($schedules as $i => $schedule)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $i + 1 }}</td>
                    <td class="px-4 py-2">{{ $schedule['kode_matkul'] }}</td>
                    <td class="px-4 py-2">{{ $schedule['nama_matkul'] }}</td>
                    <td class="px-4 py-2 text-center">{{ $schedule['sks'] }}</td>
                    <td class="px-4 py-2">{{ $schedule['kode_dosen'] }}</td>
                    <td class="px-4 py-2">{{ $schedule['nama_dosen'] }}</td>
                    <td class="px-4 py-2 whitespace-pre-wrap">
                        {{ $schedule['kelas_info'] }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
