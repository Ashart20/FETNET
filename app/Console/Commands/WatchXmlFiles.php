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
                $this->info("📂 Checking folder: $folder");
                Log::info("📂 Checking folder: $folder");

                // Ambil daftar semua file dalam folder
                $allFiles = scandir($folder);
                foreach ($allFiles as $file) {
                    if (str_ends_with($file, '.fet')) {
                        $filePath = $folder . '/' . $file;
                        $this->info("✅ File ditemukan: $filePath");
                        Log::info("✅ File ditemukan: $filePath");
                        $this->importXml($filePath);
                    }
                }

                // Cek juga dalam subfolder
                $subFolders = glob($folder . '/*', GLOB_ONLYDIR);
                foreach ($subFolders as $subFolder) {
                    $this->info("➡️ Masuk ke subfolder: $subFolder");
                    Log::info("➡️ Masuk ke subfolder: $subFolder");

                    $subFiles = scandir($subFolder);
                    foreach ($subFiles as $file) {
                        if (str_ends_with($file, '.fet')) {
                            $filePath = $subFolder . '/' . $file;
                            $this->info("✅ File ditemukan di subfolder: $filePath");
                            Log::info("✅ File ditemukan di subfolder: $filePath");
                            $this->importXml($filePath);
                        }
                    }
                    // Hapus folder setelah semua file di dalamnya diproses
                    // if (count(glob($folder . '/*')) === 0) {
                    //     rmdir($folder);
                    //    $this->info("Deleted empty folder: $folder");

                }
            }
            sleep(5); // Cek setiap 5 detik
        }
    }

    private function importXml($filePath)
    {

        $xmlContent = file_get_contents($filePath);
// Coba parsing sebagai XML
        try {
            $xml = new SimpleXMLElement($xmlContent);
        } catch (\Exception $e) {
            $this->error("Failed to parse XML from: $filePath");
            return;
        }
        event(new ScheduleUpdated());

        // Cek apakah ada bagian "Activities_List"
        if (isset($xml->Activities_List->Activity)) {
            foreach ($xml->Activities_List->Activity as $entry) {
                Schedule::create([
                    'course' => (string)$entry->Subject,
                    'lecturer' => (string)$entry->Teacher,
                    'room' => (string)$entry->Room,
                    'time_slot' => (string)$entry->Day . ' ' . $entry->Time,
                ]);
            }

            $this->info("✅ Data jadwal berhasil diimpor dari: $filePath");
        } else {
            $this->error("⚠️ Tidak ditemukan elemen jadwal dalam XML: $filePath");
        }
    }
}
