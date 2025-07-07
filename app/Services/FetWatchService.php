<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class FetWatchService
{
    public function processAvailableFetFiles(bool $includeXml = true): void
    {
        Log::info("FetWatchService: -- STARTING processAvailableFetFiles (Synchronous) --");
        $baseWatchPath = storage_path('app/fet-results/timetables');
        Log::info("FetWatchService: Base watch path set to: {$baseWatchPath}");

        $latestSubdir = $this->getLatestSubdirectory($baseWatchPath);

        if (!$latestSubdir) {
            Log::info("FetWatchService: No valid FET schedule subdirectory found in: {$baseWatchPath}. Skipping processing.");
            Log::info("FetWatchService: -- ENDING processAvailableFetFiles (No Valid Subdir Found) --");
            return;
        }

        // --- PENTING: Cek file marker di dalam subdirektori terbaru ---
        $markerFile = $latestSubdir . DIRECTORY_SEPARATOR . '.processed_ok';
        if (File::exists($markerFile)) {
            Log::info("FetWatchService: Subdirectory {$latestSubdir} already processed (marker found). Skipping parsing.");
            Log::info("FetWatchService: -- ENDING processAvailableFetFiles (Already Processed) --");
            return;
        }
        // --- END CHECK MARKER ---

        $watchPath = $latestSubdir;
        Log::info("FetWatchService: Processing latest subdirectory: {$watchPath}");

        $rii = null;
        try {
            $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($watchPath));
        } catch (\UnexpectedValueException $e) {
            Log::error("FetWatchService: Error opening directory {$watchPath}: " . $e->getMessage());
            Log::info("FetWatchService: -- ENDING processAvailableFetFiles (Dir Error) --");
            return;
        }

        $fileToProcess = null;
        // Prioritaskan file yang kemungkinan berisi jadwal lengkap
        foreach ($rii as $file) {
            if ($file->isDir()) continue;
            $path = $file->getPathname();
            $fileName = basename($path);

            $processedSubdirName = 'processed';
            $quarantineSubdirName = 'quarantine';
            $parentDirName = basename(dirname($path));

            // Kita tidak perlu memindahkan file individu ke processed/quarantine lagi
            // Tapi kita masih ingin melewati jika ada file lock
            if (str_ends_with($path, '.lock')) {
                continue;
            }

            // --- Perbaikan: Hanya cari file utama, tidak perlu mengecek processed/quarantine di sini
            // Karena kita sudah memfilter subdirektori processed/quarantine di getLatestSubdirectory
            if (preg_match('/_data_and_timetable\.fet$/i', $fileName)) {
                $fileToProcess = $path; break;
            } elseif (preg_match('/index\.html$/i', $fileName)) {
                $fileToProcess = $path; break;
            } elseif ($includeXml && preg_match('/_timetables\.xml$/i', $fileName)) {
                $fileToProcess = $path; break;
            } elseif (preg_match('/\.fet$/i', $fileName)) {
                $fileToProcess = $path; // Fallback jika tidak ada yang lebih spesifik
            } elseif ($includeXml && preg_match('/\.xml$/i', $fileName)) {
                $fileToProcess = $path; // Fallback jika tidak ada yang lebih spesifik
            }
        }

        if (!$fileToProcess) {
            Log::info("FetWatchService: No primary schedule file found in latest subdirectory: {$watchPath}. Skipping parsing.");
            Log::info("FetWatchService: -- ENDING processAvailableFetFiles (No Primary File) --");
            return;
        }

        try {
            Artisan::call('fet:parse', ['file' => $fileToProcess]);
            Log::info("FetWatchService: Successfully called fet:parse for file: {$fileToProcess}. Output: " . Artisan::output());

            // --- PENTING: Setelah sukses, buat file marker di folder itu ---
            File::put($markerFile, 'Processed at ' . now());
            Log::info("FetWatchService: Marked subdirectory {$latestSubdir} as processed with marker file.");

        } catch (\Throwable $e) {
            Log::error("FetWatchService: Error calling fet:parse for {$fileToProcess}: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            // --- Pertimbangkan membuat marker gagal atau memindahkan seluruh folder ke karantina ---
            // Untuk saat ini, kita bisa buat marker gagal agar tidak diproses lagi jika selalu gagal
            File::put($latestSubdir . DIRECTORY_SEPARATOR . '.failed_marker', 'Failed at ' . now() . ' with error: ' . $e->getMessage());
            Log::warning("FetWatchService: Marked subdirectory {$latestSubdir} as failed with marker file.");
        }

        Log::info("FetWatchService: -- FINISHED processAvailableFetFiles (Synchronous) --");
    }

    // getLatestSubdirectory tetap sama (tanpa perubahan di sini)
    protected function getLatestSubdirectory(string $basePath): ?string
    {
        Log::info("FetWatchService: getLatestSubdirectory called for: {$basePath}");
        if (!File::isDirectory($basePath)) {
            Log::warning("FetWatchService: Base FET watch path does not exist: {$basePath}");
            return null;
        }

        $allSubdirectories = File::directories($basePath);
        Log::info("FetWatchService: All subdirectories found: " . implode(', ', array_map('basename', $allSubdirectories)));

        $validSubdirectories = [];
        foreach ($allSubdirectories as $dir) {
            $dirName = basename($dir);
            // Abaikan processed dan quarantine
            if ($dirName !== 'processed' && $dirName !== 'quarantine') {
                // Tambahan: Abaikan juga folder yang sudah ada marker .processed_ok
                if (!File::exists($dir . DIRECTORY_SEPARATOR . '.processed_ok')) { // <--- TAMBAH INI
                    $validSubdirectories[] = $dir;
                } else {
                    Log::info("FetWatchService: Skipping already processed subdirectory (has .processed_ok marker): {$dir}");
                }
            }
        }

        if (empty($validSubdirectories)) {
            Log::info("FetWatchService: No valid non-processed/non-quarantine subdirectories found in base path: {$basePath}");
            return null;
        }

        usort($validSubdirectories, function($a, $b) {
            $timeA = File::lastModified($a);
            $timeB = File::lastModified($b);
            if ($timeA !== $timeB) {
                return $timeB <=> $timeA;
            }
            return basename($b) <=> basename($a);
        });

        $latestDir = $validSubdirectories[0];
        Log::info("FetWatchService: Latest valid subdirectory identified: {$latestDir}");
        return $latestDir;
    }
}
