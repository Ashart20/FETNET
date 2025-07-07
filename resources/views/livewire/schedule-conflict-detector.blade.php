{{-- resources/views/livewire/schedule-conflict-detector.blade.php --}}

<div x-data="{
        showNotification: false, // Digunakan untuk mengontrol visibilitas notifikasi
        notificationMessage: '',
        notificationType: '', // 'success' atau 'error'
        init() {
            // Listener untuk menampilkan notifikasi konflik/bersih
            Livewire.on('showConflictNotification', (data) => {
                this.notificationMessage = 'Ditemukan ' + data.count + ' konflik jadwal!';
                this.notificationType = 'error';
                this.showNotification = true;
                // Tidak perlu setTimeout di sini jika Anda ingin dia tetap terlihat sampai konflik diatasi
                // atau jika ada tombol close
            });

            Livewire.on('showCleanNotification', (message) => {
                this.notificationMessage = message || 'Jadwal Anda bersih dari bentrokan.';
                this.notificationType = 'success';
                this.showNotification = true;
                // Biarkan notifikasi bersih hilang otomatis
                setTimeout(() => { this.showNotification = false; }, 5000);
            });

            // Listener untuk menutup notifikasi secara manual atau setelah refresh
            Livewire.on('clearConflictAlert', () => {
                this.showNotification = false;
            });
        }
     }"
     x-show="showNotification" {{-- Kontrol visibilitas notifikasi utama --}}
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform -translate-y-2"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     x-transition:leave="transition ease-in duration-500"
     x-transition:leave-start="opacity-100 transform translate-y-0"
     x-transition:leave-end="opacity-0 transform -translate-y-2"
     class="rounded relative mb-4 {{-- Kelas dasar untuk notifikasi --}}"
     :class="{
        'bg-red-100 border border-red-400 text-red-700 dark:bg-red-900 dark:border-red-700 dark:text-red-300': notificationType === 'error',
        'bg-green-100 border border-green-400 text-green-700 dark:bg-green-900 dark:border-green-700 dark:text-green-300': notificationType === 'success',
     }"
     role="alert"
     style="display: none;" {{-- Penting untuk Alpine: Hindari FOUC --}}
>
    <div class="px-4 py-3">
        <strong class="font-bold" x-text="notificationType === 'error' ? 'Perhatian!' : 'Sukses!'"></strong>
        <span class="block sm:inline" x-text="notificationMessage"></span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg @click="showNotification = false; $wire.clearConflictAlert()" class="fill-current h-6 w-6 cursor-pointer"
                 :class="{ 'text-red-500': notificationType === 'error', 'text-green-500': notificationType === 'success' }"
                 role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.107l-2.651 3.742a1.2 1.2 0 1 1-1.697-1.697l3.742-2.651-3.742-2.651a1.2 1.2 0 1 1 1.697-1.697l2.651 3.742 2.651-3.742a1.2 1.2 0 1 1 1.697 1.697l-3.742 2.651 3.742 2.651a1.2 1.2 0 0 1 0 1.697z"/></svg>
        </span>
    </div>
</div>

{{-- Detail Konflik (Tetap akan tampil jika $this->conflicts tidak kosong) --}}
@if (!empty($this->conflicts))
    <h3 class="text-lg font-semibold text-red-600 mb-3 dark:text-red-400">Detail Konflik Jadwal:</h3>
    <div class="overflow-x-auto rounded-lg shadow-md mb-4"> {{-- Tambahkan shadow dan margin --}}
        {{-- Ubah warna tabel untuk dark mode --}}
        <table class="min-w-full bg-white border border-gray-300 dark:bg-dark-secondary dark:border-gray-700">
            <thead class="bg-gray-100 dark:bg-dark-tertiary">
            <tr>
                <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-700 text-left text-gray-700 dark:text-white">Jenis Konflik</th>
                <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-700 text-left text-gray-700 dark:text-white">Sumber Daya</th>
                <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-700 text-left text-gray-700 dark:text-white">Waktu</th>
                <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-700 text-left text-gray-700 dark:text-white">Sesi Terlibat</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($this->conflicts as $conflict)
                <tr class="bg-red-50 dark:bg-red-800"> {{-- Ubah warna baris konflik --}}
                    <td class="py-2 px-4 border-b border-gray-300 dark:border-gray-700 text-red-700 dark:text-red-300">{{ $conflict['type'] }}</td>
                    <td class="py-2 px-4 border-b border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white">{{ $conflict['resource'] }}</td>
                    <td class="py-2 px-4 border-b border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white">{{ $conflict['time'] }}</td>
                    <td class="py-2 px-4 border-b border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white">
                        <ul>
                            @foreach ($conflict['sessions'] as $session)
                                <li>- {{ $session }}</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
