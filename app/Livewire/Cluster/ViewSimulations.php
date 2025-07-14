<?php

namespace App\Livewire\Cluster;

use App\Jobs\ApplyClusterScheduleJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Mary\Traits\Toast;

class ViewSimulations extends Component
{
    use Toast;

    public array $simulations = [];

    public function mount()
    {
        $this->loadSimulations();
    }

    public function loadSimulations()
    {
        clearstatcache();

        $this->simulations = []; // Kosongkan array untuk memastikan data baru yang dimuat
        $user = Auth::user();
        if (! $user->cluster_id) {
            return;
        }

        $clusterCode = $user->cluster->code;
        $directory = storage_path("app/public/simulations/{$clusterCode}/timetables");

        if (! File::isDirectory($directory)) {
            return;
        }

        $simulationFolders = File::directories($directory);
        rsort($simulationFolders);

        foreach ($simulationFolders as $folderPath) {
            $folderName = basename($folderPath);

            // 1. Cari file yang cocok dengan pola di dalam folder
            $foundFiles = File::glob("{$folderPath}/*_index.html");

            // 2. Jika tidak ada file yang cocok, lewati iterasi ini
            if (empty($foundFiles)) {
                continue;
            }

            // 3. Ambil nama file yang benar dari path lengkapnya
            $correctFileName = basename($foundFiles[0]);
            $this->simulations[] = [
                'folder' => $folderName,
                'name' => 'Simulasi '.str_replace(['simulasi_', '_'], ' ', $folderName),
                // Gunakan variabel $correctFileName yang sudah ditemukan
                'url' => route('cluster.simulations.show', ['simulation_folder' => $folderName, 'file_name' => $correctFileName]),
                'created_at' => date('d M Y, H:i:s', File::lastModified($folderPath)),
            ];
        }
    }

    /**
     * PERBAIKAN 2: Method baru untuk dipanggil oleh tombol refresh.
     */
    public function refreshList()
    {
        $this->loadSimulations();
        $this->toast(type: 'info', title: 'Daftar simulasi telah diperbarui.');
    }

    public function applySimulation(string $simulationFolder)
    {
        $user = Auth::user();
        if (! $user->cluster_id) {
            $this->toast(type: 'error', title: 'Aksi Gagal!', description: 'Anda tidak terhubung ke cluster manapun.');

            return;
        }

        ApplyClusterScheduleJob::dispatch($user->cluster_id, $simulationFolder);
        $this->toast(type: 'info', title: 'Proses Penerapan Dimulai', description: 'Jadwal resmi untuk cluster Anda sedang diperbarui di latar belakang.');
    }

    public function deleteSimulation(string $simulationFolder)
    {
        // 1. Dapatkan user dan cluster code
        $user = Auth::user();
        $clusterCode = $user->cluster->code;

        // 2. Buat path lengkap ke direktori yang akan dihapus
        $directoryPath = storage_path("app/public/simulations/{$clusterCode}/timetables/{$simulationFolder}");

        // 3. Hapus direktori jika ada
        if (File::isDirectory($directoryPath)) {
            File::deleteDirectory($directoryPath);
            $this->toast(type: 'success', title: 'Berhasil!', description: 'Folder simulasi telah dihapus.');
        } else {
            $this->toast(type: 'error', title: 'Gagal!', description: 'Folder simulasi tidak ditemukan.');
        }

        // 4. Muat ulang daftar simulasi untuk memperbarui tampilan
        $this->loadSimulations();
    }

    public function deleteAllSimulations()
    {
        // 1. Dapatkan user dan cluster code
        $user = Auth::user();
        $clusterCode = $user->cluster->code;

        // 2. Tentukan path ke direktori utama yang berisi semua folder simulasi
        $baseDirectory = storage_path("app/public/simulations/{$clusterCode}/timetables");

        // 3. Hapus seluruh direktori beserta isinya
        if (File::isDirectory($baseDirectory)) {
            File::deleteDirectory($baseDirectory);
        }

        // 4. Buat kembali direktori yang kosong
        File::makeDirectory($baseDirectory, 0775, true);

        // 5. Beri notifikasi dan muat ulang daftar
        $this->toast(type: 'success', title: 'Semua Simulasi Dihapus!', description: 'Seluruh riwayat simulasi telah berhasil dihapus.');
        $this->loadSimulations();
    }

    public function render()
    {
        return view('livewire.cluster.view-simulations')->layout('layouts.app');
    }
}
