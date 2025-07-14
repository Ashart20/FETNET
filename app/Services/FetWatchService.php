<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class FetWatchService
{
    public function processAvailableFetFiles(bool $includeXml = true): void
    {
        Log::info('FetWatchService: -- STARTING processAvailableFetFiles (Synchronous) --');
        $baseWatchPath = storage_path('app/fet-results/timetables');
        Log::info("FetWatchService: Base watch path set to: {$baseWatchPath}");

        $latestSubdir = $this->getLatestSubdirectory($baseWatchPath);

        if (! $latestSubdir) {
            Log::info("FetWatchService: No valid FET schedule subdirectory found in: {$baseWatchPath}. Skipping processing.");
            Log::info('FetWatchService: -- ENDING processAvailableFetFiles (No Valid Subdir Found) --');

            return;
        }

        $markerFile = $latestSubdir.DIRECTORY_SEPARATOR.'.processed_ok';
        if (File::exists($markerFile)) {
            Log::info("FetWatchService: Subdirectory {$latestSubdir} already processed (marker found). Skipping parsing.");
            Log::info('FetWatchService: -- ENDING processAvailableFetFiles (Already Processed) --');

            return;
        }

        $watchPath = $latestSubdir;
        Log::info("FetWatchService: Processing latest subdirectory: {$watchPath}");

        $rii = null;
        try {
            $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($watchPath));
        } catch (\UnexpectedValueException $e) {
            Log::error("FetWatchService: Error opening directory {$watchPath}: ".$e->getMessage());
            Log::info('FetWatchService: -- ENDING processAvailableFetFiles (Dir Error) --');

            return;
        }

        $fileToProcess = null;
        foreach ($rii as $file) {
            if ($file->isDir()) {
                continue;
            }
            $path = $file->getPathname();
            $fileName = basename($path);

            $processedSubdirName = 'processed';
            $quarantineSubdirName = 'quarantine';
            $parentDirName = basename(dirname($path));

            if (str_ends_with($path, '.lock')) {
                continue;
            }

            if (preg_match('/_data_and_timetable\.fet$/i', $fileName)) {
                $fileToProcess = $path;
                break;
            } elseif (preg_match('/index\.html$/i', $fileName)) {
                $fileToProcess = $path;
                break;
            } elseif ($includeXml && preg_match('/_timetables\.xml$/i', $fileName)) {
                $fileToProcess = $path;
                break;
            } elseif (preg_match('/\.fet$/i', $fileName)) {
                $fileToProcess = $path; // Fallback jika tidak ada yang lebih spesifik
            } elseif ($includeXml && preg_match('/\.xml$/i', $fileName)) {
                $fileToProcess = $path; // Fallback jika tidak ada yang lebih spesifik
            }
        }

        if (! $fileToProcess) {
            Log::info("FetWatchService: No primary schedule file found in latest subdirectory: {$watchPath}. Skipping parsing.");
            Log::info('FetWatchService: -- ENDING processAvailableFetFiles (No Primary File) --');

            return;
        }

        try {
            Artisan::call('fet:parse', ['file' => $fileToProcess]);
            Log::info("FetWatchService: Successfully called fet:parse for file: {$fileToProcess}. Output: ".Artisan::output());

            File::put($markerFile, 'Processed at '.now());
            Log::info("FetWatchService: Marked subdirectory {$latestSubdir} as processed with marker file.");

        } catch (\Throwable $e) {
            Log::error("FetWatchService: Error calling fet:parse for {$fileToProcess}: ".$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            File::put($latestSubdir.DIRECTORY_SEPARATOR.'.failed_marker', 'Failed at '.now().' with error: '.$e->getMessage());
            Log::warning("FetWatchService: Marked subdirectory {$latestSubdir} as failed with marker file.");
        }

        Log::info('FetWatchService: -- FINISHED processAvailableFetFiles (Synchronous) --');
    }

    protected function getLatestSubdirectory(string $basePath): ?string
    {
        Log::info("FetWatchService: getLatestSubdirectory called for: {$basePath}");
        if (! File::isDirectory($basePath)) {
            Log::warning("FetWatchService: Base FET watch path does not exist: {$basePath}");

            return null;
        }

        $allSubdirectories = File::directories($basePath);
        Log::info('FetWatchService: All subdirectories found: '.implode(', ', array_map('basename', $allSubdirectories)));

        $validSubdirectories = [];
        foreach ($allSubdirectories as $dir) {
            $dirName = basename($dir);
            // Abaikan processed dan quarantine
            if ($dirName !== 'processed' && $dirName !== 'quarantine') {

                if (! File::exists($dir.DIRECTORY_SEPARATOR.'.processed_ok')) {
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

        usort($validSubdirectories, function ($a, $b) {
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
