<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class FetWatchService
{
    public function processAvailableFetFiles(bool $includeXml = false): void
    {
        $watchPath = storage_path('app/fet-results/timetables');

        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($watchPath));
        $files = [];

        foreach ($rii as $file) {
            if ($file->isDir()) continue;
            $path = $file->getPathname();

            if (
                !str_contains($path, '/processed/') &&
                !str_contains($path, '/quarantine/') &&
                !str_ends_with($path, '.lock') &&
                (
                    preg_match('/\.fet$/i', $path) ||
                    ($includeXml && preg_match('/\.xml$/i', $path))
                )
            ) {
                $files[] = $path;
            }
        }

        foreach ($files as $file) {
            try {
                Artisan::call('fet:parse', ['file' => $file]);
                Log::info("FET parsed: {$file}");
            } catch (\Throwable $e) {
                Log::error("FET parsing error: " . $e->getMessage());
            }
        }
    }
}
