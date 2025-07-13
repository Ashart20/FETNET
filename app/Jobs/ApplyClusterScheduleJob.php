<?php

namespace App\Jobs;

use App\Models\Cluster;
use App\Models\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ApplyClusterScheduleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $clusterId;
    protected string $simulationFolder;

    public function __construct(int $clusterId, string $simulationFolder)
    {
        $this->clusterId = $clusterId;
        $this->simulationFolder = $simulationFolder;
    }

    public function handle(): void
    {
        Log::info("MEMULAI PENERAPAN JADWAL dari simulasi '{$this->simulationFolder}' untuk Cluster ID: {$this->clusterId}.");

        $cluster = Cluster::find($this->clusterId);
        if (!$cluster) {
            Log::error("Penerapan gagal: Cluster dengan ID {$this->clusterId} tidak ditemukan.");
            return;
        }

        try {
            // 1. Hapus jadwal lama (logika ini sudah benar)
            $prodiIds = $cluster->prodis->pluck('id');
            if ($prodiIds->isNotEmpty()) {
                Log::info("Menghapus jadwal lama untuk prodi ID: " . $prodiIds->implode(', '));
                $scheduleIdsToDelete = Schedule::whereHas('activity', fn ($q) => $q->whereIn('prodi_id', $prodiIds))->pluck('id');
                if ($scheduleIdsToDelete->isNotEmpty()) {
                    DB::table('schedule_teacher')->whereIn('schedule_id', $scheduleIdsToDelete)->delete();
                    Schedule::whereIn('id', $scheduleIdsToDelete)->delete();
                    Log::info("Jadwal lama untuk cluster {$cluster->name} berhasil dihapus.");
                }
            }

            // 2. Tentukan path ke file hasil simulasi yang akan diparse
            $outputFileName = "{$this->simulationFolder}_data_and_timetable.fet";

            $outputFilePath = storage_path("app/public/simulations/{$cluster->code}/timetables/{$this->simulationFolder}/{$outputFileName}");

            if (File::exists($outputFilePath)) {
                // 3. Panggil Artisan command untuk mem-parse file
                Log::info("Memulai parsing untuk file: {$outputFilePath}");
                Artisan::call('fet:parse', [
                    'file' => $outputFilePath,
                    '--no-cleanup' => true
                ]);
                Log::info("Parsing untuk simulasi '{$this->simulationFolder}' selesai.");
            } else {
                Log::error("Parsing GAGAL: File hasil tidak ditemukan di path yang diharapkan: {$outputFilePath}");
            }

        } catch (\Exception $e) {
            Log::critical("GAGAL TOTAL SAAT MENERAPKAN JADWAL SIMULASI: " . $e->getMessage());
            throw $e;
        }
    }
}
