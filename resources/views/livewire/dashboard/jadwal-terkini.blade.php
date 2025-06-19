<div class="bg-white p-6 rounded-xl shadow-md animate-fade-in">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">📅 Jadwal Terkini</h2>
        <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
            <i class="fas fa-plus mr-2"></i> Tambah Jadwal
        </button>
    </div>

    <table class="w-full text-sm text-gray-700">
        <thead>
        <tr class="text-left bg-gray-100 text-gray-600 uppercase text-xs">
            <th class="py-3 px-4 rounded-tl-lg">Mata Kuliah</th>
            <th class="py-3 px-4">Dosen</th>
            <th class="py-3 px-4">Waktu</th>
            <th class="py-3 px-4 rounded-tr-lg">Status</th>
        </tr>
        </thead>
        <tbody class="divide-y">
        <tr class="hover:bg-gray-50 transition">
            <td class="py-4 px-4 font-medium">Pemrograman Web</td>
            <td class="px-4">Dosen A</td>
            <td class="px-4 flex items-center gap-2">
                <i class="fas fa-clock text-indigo-500"></i> Senin, 08:00
            </td>
            <td class="px-4">
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                        <i class="fas fa-check-circle text-green-500"></i> Aktif
                    </span>
            </td>
        </tr>

        <tr class="hover:bg-gray-50 transition">
            <td class="py-4 px-4 font-medium">Sistem Informasi</td>
            <td class="px-4">Dosen B</td>
            <td class="px-4 flex items-center gap-2">
                <i class="fas fa-clock text-indigo-500"></i> Selasa, 10:00
            </td>
            <td class="px-4">
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">
                        <i class="fas fa-hourglass-half text-yellow-500"></i> Pending
                    </span>
            </td>
        </tr>

        <tr class="hover:bg-gray-50 transition">
            <td class="py-4 px-4 font-medium">Algoritma</td>
            <td class="px-4">Dosen C</td>
            <td class="px-4 flex items-center gap-2">
                <i class="fas fa-clock text-indigo-500"></i> Rabu, 13:00
            </td>
            <td class="px-4">
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">
                        <i class="fas fa-times-circle text-red-500"></i> Dibatalkan
                    </span>
            </td>
        </tr>
        </tbody>
    </table>
</div>
