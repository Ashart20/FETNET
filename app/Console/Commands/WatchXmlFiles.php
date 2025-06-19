<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class WatchXmlFiles extends Command
{
    protected $signature = 'watch:fet {--include-xml}';
    protected $description = 'Watch and process .fet (and optionally .xml) files';

    protected string $watchPath;
    protected string $processedPath;
    protected string $quarantinePath;
    protected array $attempts = [];

    public function __construct()
    {
        parent::__construct();
        $this->watchPath = storage_path('app/fet-results/timetables');
        $this->processedPath = $this->watchPath . '/processed';
        $this->quarantinePath = $this->watchPath . '/quarantine';
    }

    public function handle()
    {
        $this->info("📂 Watching folder: {$this->watchPath}");

        while (true) {
            $this->processAllFiles();
            sleep(5); // polling interval
        }
    }

    private function processAllFiles()
    {
        $includeXml = $this->option('include-xml');
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->watchPath));
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

        if (empty($files)) {
            $this->line("⏳ No new .fet files found.");
            return;
        }

        foreach ($files as $file) {
            $this->processFileWithRetry($file);
        }
    }

    private function processFileWithRetry(string $file)
    {
        $maxAttempts = 3;
        $lockFile = $file . '.lock';

        if (file_exists($lockFile) && (time() - filemtime($lockFile)) < 300) {
            $this->line("🔒 Locked: " . basename($file));
            return;
        }

        touch($lockFile);
        $attempt = $this->attempts[$file] = ($this->attempts[$file] ?? 0) + 1;

        $this->info("\n🚀 Processing file ({$attempt}/{$maxAttempts}): " . basename($file));

        try {
            Artisan::call('fet:parse', ['file' => $file]);
            $this->info(Artisan::output());
            $this->moveToProcessed($file);
            unset($this->attempts[$file]);
        } catch (\Throwable $e) {
            $this->error("❌ Error: " . $e->getMessage());
            Log::error("Watcher failed: {$e->getMessage()}");

            if ($attempt >= $maxAttempts) {
                $this->moveToQuarantine($file);
                unset($this->attempts[$file]);
            }
        } finally {
            @unlink($lockFile);
        }
    }

    private function moveToProcessed(string $file)
    {
        $this->moveFile($file, $this->processedPath, "✅ Moved to processed: ");
    }

    private function moveToQuarantine(string $file)
    {
        $this->moveFile($file, $this->quarantinePath, "⚠️ Moved to quarantine: ");
    }

    private function moveFile(string $file, string $targetDir, string $message)
    {
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $name = basename($file);
        $newPath = $targetDir . '/' . now()->format('Ymd_His_') . $name;

        rename($file, $newPath);
        $this->line($message . basename($newPath));
    }
}
