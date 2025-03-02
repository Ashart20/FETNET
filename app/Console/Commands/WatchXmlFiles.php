<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Schedule;
use SimpleXMLElement;
use Illuminate\Support\Facades\Storage;
use App\Events\ScheduleUpdated;
use Illuminate\Support\Facades\Log;

class WatchXmlFiles extends Command
{
    protected $signature = 'watch:xml';
    protected $description = 'Watch XML files in the watcher directory and import automatically';

    public function handle()
    {
        $watchDir = base_path('watcher');

        while (true) {
            $this->info("Checking for new XML folders...");

            // Cari semua folder di dalam watcher
            $folders = glob($watchDir . '/*', GLOB_ONLYDIR);

            foreach ($folders as $folder) {
                if (!is_dir($folder)) continue;

                $this->info("📂 Checking folder: $folder");
                Log::info("📂 Checking folder: $folder");

                // Ambil file .fet di dalam folder utama
                $files = glob($folder . '/*.fet');
                foreach ($files as $file) {
                    $this->info("✅ File ditemukan: $file");
                    Log::info("✅ File ditemukan: $file");
                    $this->importXml($file);
                }

                // Cek subfolder
                $subFolders = glob($folder . '/*', GLOB_ONLYDIR);
                foreach ($subFolders as $subFolder) {
                    if (!is_dir($subFolder)) continue;

                    $this->info("➡️ Masuk ke subfolder: $subFolder");
                    Log::info("➡️ Masuk ke subfolder: $subFolder");

                    $subFiles = glob($subFolder . '/*.fet');
                    foreach ($subFiles as $file) {
                        $this->info("✅ File ditemukan di subfolder: $file");
                        Log::info("✅ File ditemukan di subfolder: $file");
                        $this->importXml($file);
                    }
                }

                // Hapus folder jika kosong
                if (count(glob($folder . '/*')) === 0) {
                    rmdir($folder);
                    $this->info("Deleted empty folder: $folder");
                }
            }
            sleep(5); // Cek setiap 5 detik
        }
    }

    private function importXml($filePath)
    {
        Log::info("📂 Memproses file: $filePath");

        if (!file_exists($filePath)) {
            Log::error("❌ File tidak ditemukan: $filePath");
            return;
        }

        $xmlContent = file_get_contents($filePath);

        try {
            $xml = new SimpleXMLElement($xmlContent);
        } catch (\Exception $e) {
            Log::error("❌ Gagal membaca XML dari: $filePath - " . $e->getMessage());
            return;
        }

        Log::info("✅ Berhasil membaca XML dari: $filePath");

        if (!isset($xml->Activities_List->Activity)) {
            Log::error("⚠️ Struktur XML tidak valid atau kosong: $filePath");
            $this->error("⚠️ Tidak ditemukan elemen jadwal dalam XML: $filePath");
            return;
        }

        foreach ($xml->Activities_List->Activity as $entry) {
            Schedule::create([
                'course' => (string)$entry->Subject,
                'lecturer' => (string)$entry->Teacher,
                'room' => (string)$entry->Room,
                'time_slot' => (string)$entry->Day . ' ' . $entry->Time,
            ]);
        }

        $this->info("✅ Data jadwal berhasil diimpor dari: $filePath");
        event(new ScheduleUpdated());
    }
}
