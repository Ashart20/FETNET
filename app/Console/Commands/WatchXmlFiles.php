<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Schedule;
use App\Models\Room;
use App\Models\TimeSlot;
use SimpleXMLElement;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class WatchXmlFiles extends Command
{
    protected $signature = 'watch:fet';
    protected $description = 'Monitor and process FET .fet files';

    protected $processedDir;
    protected $lockFiles = [];
    protected $attempts = [];

    public function handle()
    {
        $watchPath = config('fet.watch_path', storage_path('app/fet-results'));
        $this->processedDir = config('fet.processed_path', $watchPath.'/processed');

        $this->info("🕵️ Starting FET watcher at: {$watchPath}");
        $this->info("⌚ Polling interval: " . config('fet.interval', 5) . "s");
        $this->info("🔐 Lock files dir: {$watchPath}");
        $this->info("📂 Processed dir: {$this->processedDir}");

        while (true) {
            try {
                $this->logDirectoryState($watchPath);
                $this->processFiles($watchPath);
                sleep(config('fet.interval', 5));
            } catch (\Exception $e) {
                Log::error("Watcher error: " . $e->getMessage());
                $this->error("⚠️ Critical Error: " . $e->getMessage());
                sleep(10);
            }
        }
    }

    private function logDirectoryState($path)
    {
        $this->info("\n=== Directory State ===");
        $this->info("Path: {$path}");
        $this->info("Items:");
        $path = rtrim($path, '/');

        try {
            $items = scandir($path);
            foreach($items as $item) {
                if($item === '.' || $item === '..') continue;

                $fullPath = rtrim($path, '/').'/'.$item;
                $this->line(sprintf(
                    " - %s [%s] [%s]",
                    $item,
                    is_dir($fullPath) ? 'DIR' : 'FILE',
                    date('Y-m-d H:i:s', filemtime($fullPath))
                ));
            }
        } catch (\Exception $e) {
            $this->error("Failed to scan directory: " . $e->getMessage());
        }
    }
    private function processSingleFile($filePath)
    {
        $this->info("🔍 Processing: " . basename($filePath));
        Log::info("Processing FET file: {$filePath}");

        $xml = $this->loadAndValidateFetFile($filePath);

        DB::transaction(function () use ($xml) {
            $this->processRooms($xml);
            $this->processSchedules($xml);
        });

        $this->moveToProcessed($filePath);
    }
    private function findFetFiles($path)
    {
        // Pattern pencarian file .FET/.fet
        $pattern = rtrim($path, '/') . '/**/*.{fet,FET}';

        $this->info("🔎 Searching with pattern: " . $pattern);

        $files = glob($pattern, GLOB_BRACE | GLOB_NOSORT);

        // Logging untuk debug
        $this->info("📁 Found " . count($files) . " files");
        foreach($files as $file) {
            $this->line(" - " . basename($file));
        }

        // Filter out files in processed directory
        $files = array_filter($files, function($file) use ($path) {
            return !str_contains($file, $path.'/processed');
        });

        return $files;
    }
    private function processFiles($basePath)
    {
        $files = $this->findFetFiles($basePath); // <-- PASTIKAN INI
        // ...

        if(empty($files)) {
            $this->info("⏳ No files found to process");
            return;
        }

        foreach($files as $file) {
            $this->processSingleFileWithRetry($file);
        }


    }

    private function processSingleFileWithRetry($file)
    {
        $maxAttempts = 3;
        $lockFile = $file . '.lock';

        try {
            if($this->isProcessing($file)) {
                $this->info("⏳ File is being processed by another process: " . basename($file));
                return;
            }

            touch($lockFile);
            $this->attempts[$file] = ($this->attempts[$file] ?? 0) + 1;

            $this->info("\n🚀 Processing file (" . $this->attempts[$file] . "/{$maxAttempts}): " . basename($file));
            $this->processSingleFile($file);
            $this->moveToProcessed($file);
            unset($this->attempts[$file]);

        } catch (\Exception $e) {
            $this->error("❌ Attempt " . $this->attempts[$file] . " failed: " . $e->getMessage());

            if($this->attempts[$file] >= $maxAttempts) {
                $this->error("🔥 Moving to quarantine: " . basename($file));
                $this->moveToQuarantine($file);
                unset($this->attempts[$file]);
            }
        } finally {
            if(file_exists($lockFile)) {
                unlink($lockFile);
            }
        }
    }

    private function isProcessing($file)
    {
        $lockFile = $file . '.lock';

        return file_exists($lockFile) &&
            (time() - filemtime($lockFile)) < 300; // 5 menit timeout
    }
    private function loadAndValidateFetFile($filePath)
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }

        $xml = simplexml_load_file($filePath);

        if (!$xml || $xml->getName() !== 'fet') {
            throw new \Exception("Invalid FET file format");
        }

        return $xml;
    }
    private function processRooms($xml)
    {
        if (!isset($xml->Rooms_List)) return;

        foreach ($xml->Rooms_List->Room as $room) {
            Room::updateOrCreate(
                ['name' => (string)$room->Name],
                [
                    'capacity' => (int)$room->Capacity,
                    'type' => $this->determineRoomType((string)$room->Name)
                ]
            );
        }
    }

    private function processSchedules($xml)
    {
        if (!isset($xml->Activities_List)) return;

        foreach ($xml->Activities_List->Activity as $activity) {
            try {
                $this->validateActivity($activity);

                Schedule::updateOrCreate(
                    ['fet_id' => (string)$activity->Id],
                    [
                        'subject' => (string)$activity->Subject,
                        'teacher' => (string)$activity->Teacher,
                        'room_id' => $this->getRoomId((string)$activity->Room),
                        'time_slot_id' => $this->getTimeSlotId($activity)
                    ]
                );

            } catch (\Exception $e) {
                Log::error("Activity error: " . $e->getMessage());
            }
        }
    }
    private function validateActivity($activity)
    {
        $required = ['Id', 'Subject', 'Teacher', 'Day', 'Hour'];
        foreach ($required as $field) {
            if (empty((string)$activity->$field)) {
                throw new \Exception("Missing field {$field}");
            }
        }

        // Jadikan Room opsional dengan default value
        if(empty((string)$activity->Room)) {
            $activity->Room = 'UNASSIGNED';
            $this->warn("⚠️ Room not specified for activity {$activity->Id}, using default");
        }
    }

    private function getRoomId($roomName)
    {
        $room = Room::where('name', $roomName)->first();
        if (!$room) {
            throw new \Exception("Room not found: {$roomName}");
        }
        return $room->id;
    }

    private function getTimeSlotId($activity)
    {
        $start = Carbon::parse((string)$activity->Hour);
        $end = $start->copy()->addMinutes((int)$activity->Duration * 45);

        return TimeSlot::firstOrCreate([
            'day' => strtolower((string)$activity->Day),
            'start_time' => $start->format('H:i'),
            'end_time' => $end->format('H:i')
        ])->id;
    }

    private function determineRoomType($name)
    {
        $name = strtolower($name);
        return match(true) {
            str_contains($name, 'lab') => 'laboratory',
            str_contains($name, 'auditorium') => 'auditorium',
            default => 'classroom'
        };
    }
    private function moveToProcessed($filePath)
    {
        $basePath = config('fet.watch_path');
        $relativePath = str_replace($basePath, '', $filePath);

        // Pastikan tidak ada duplikasi 'processed' di path
        $destination = $this->processedDir . '/' . ltrim($relativePath, '/');

        // Buat direktori tujuan
        $destinationDir = dirname($destination);
        if (!file_exists($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        if (rename($filePath, $destination)) {
            $this->info("✅ Berhasil dipindahkan ke: " . str_replace($this->processedDir, '', $destination));
        } else {
            throw new \Exception("Gagal memindahkan file ke: $destination");
        }
    }
    public function __construct()
    {
        parent::__construct();
        $this->processedDir = storage_path('app/fet-results/processed'); // Sesuaikan dengan path
    }
}
