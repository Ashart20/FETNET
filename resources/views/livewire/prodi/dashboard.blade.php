<div>
    {{-- Header Halaman --}}
    <x-mary-header :title="'Selamat Datang, ' . auth()->user()->name"
                   :subtitle="'Anda login sebagai Admin Program Studi: ' . (auth()->user()->prodi->nama_prodi ?? 'N/A')">
    </x-mary-header>

    {{-- Kartu Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <x-mary-stat
            title="Total Dosen"
            :value="$this->stats['totalDosen']"
            icon="o-academic-cap"
            description="Dosen di prodi & cluster Anda"
            class="bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-300" />
        <x-mary-stat
            title="Total Mata Kuliah"
            :value="$this->stats['totalMatkul']"
            icon="o-book-open"
            description="Mata kuliah di prodi Anda"
            class="bg-orange-100 dark:bg-orange-900/50 text-orange-600 dark:text-orange-300" />
        <x-mary-stat
            title="Total Aktivitas"
            :value="$this->stats['totalAktivitas']"
            icon="o-sparkles"
            description="Kegiatan belajar-mengajar"
            class="bg-green-100 dark:bg-green-900/50 text-green-600 dark:text-green-300" />
    </div>

    {{-- Menu Pengelolaan Data --}}
    <div class="mt-10">
        <x-mary-header title="Menu Pengelolaan Data" subtitle="Lengkapi semua data sebelum generate jadwal." with-separator />
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
            <x-mary-card title="Manajemen Dosen" icon="o-users" shadow>
                <p class="text-sm text-gray-500">Tambah, edit, dan hapus data dosen.</p>
                <x-slot:actions>
                    <x-mary-button label="Buka" :link="route('prodi.teachers')" class="btn-primary btn-sm" />
                </x-slot:actions>
            </x-mary-card>
            <x-mary-card title="Manajemen Matkul" icon="o-book-open" shadow>
                <p class="text-sm text-gray-500">Kelola data mata kuliah dan SKS.</p>
                <x-slot:actions>
                    <x-mary-button label="Buka" :link="route('prodi.subjects')" class="btn-primary btn-sm" />
                </x-slot:actions>
            </x-mary-card>
            <x-mary-card title="Kelompok Mahasiswa" icon="o-user-group" shadow>
                <p class="text-sm text-gray-500">Buat struktur kelas secara hierarkis.</p>
                <x-slot:actions>
                    <x-mary-button label="Buka" :link="route('prodi.student-groups')" class="btn-primary btn-sm" />
                </x-slot:actions>
            </x-mary-card>
            <x-mary-card title="Manajemen Aktivitas" icon="o-sparkles" shadow>
                <p class="text-sm text-gray-500">Rangkai dosen, matkul, dan kelompok.</p>
                <x-slot:actions>
                    <x-mary-button label="Buka" :link="route('prodi.activities')" class="btn-primary btn-sm" />
                </x-slot:actions>
            </x-mary-card>
            <x-mary-card title="Batasan Waktu Dosen" icon="o-clock" shadow>
                <p class="text-sm text-gray-500">Atur jadwal ketersediaan para dosen.</p>
                <x-slot:actions>
                    <x-mary-button label="Buka" :link="route('prodi.teacher-constraints')" class="btn-primary btn-sm" />
                </x-slot:actions>
            </x-mary-card>
            <x-mary-card title="Batasan Waktu Mahasiswa" icon="o-calendar-days" shadow>
                <p class="text-sm text-gray-500">Atur jadwal istirahat untuk mahasiswa.</p>
                <x-slot:actions>
                    <x-mary-button label="Buka" :link="route('prodi.student-group-constraints')" class="btn-primary btn-sm" />
                </x-slot:actions>
            </x-mary-card>
        </div>
    </div>

</div>
