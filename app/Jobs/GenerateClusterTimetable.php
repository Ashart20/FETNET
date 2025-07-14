<?php

namespace App\Jobs;

use App\Models\Cluster;
use App\Models\User;
use App\Services\ClusterFetFileGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Throwable;

class GenerateClusterTimetable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1800;

    public int $tries = 1;

    protected User $user;

    protected int $clusterId;

    public function __construct(User $user, int $clusterId)
    {
        $this->user = $user;
        $this->clusterId = $clusterId;
    }

    public function handle(ClusterFetFileGeneratorService $fetFileGenerator): void
    {
        Log::info("MEMULAI PROSES SIMULASI JADWAL UNTUK CLUSTER ID: {$this->clusterId}");
        try {
            $inputFilePath = $fetFileGenerator->generateForCluster($this->clusterId);
            Log::info("File input simulasi .fet berhasil dibuat di: {$inputFilePath}");
            $this->runFetEngine($inputFilePath);
        } catch (\Exception $e) {
            Log::error('GAGAL PADA PROSES SIMULASI CLUSTER: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
        Log::info("PROSES SIMULASI JADWAL UNTUK CLUSTER ID: {$this->clusterId} SELESAI.");
    }

    private function runFetEngine(string $inputFilePath): void
    {
        $executablePath = config('fet.executable_path');
        $qtLibsPath = config('fet.qt_library_path');
        $timeout = config('fet.timeout', 1800);

        $clusterCode = Cluster::find($this->clusterId)->code ?? 'unknown-cluster';
        $outputDir = storage_path("app/public/simulations/{$clusterCode}");
        File::ensureDirectoryExists($outputDir);

        $inputFileDirectory = dirname($inputFilePath);
        $inputFileName = basename($inputFilePath);

        Log::info("Menjalankan simulasi FET untuk Cluster: {$clusterCode}");
        Log::info("Direktori Kerja: {$inputFileDirectory}");
        Log::info("File Input: {$inputFileName}");
        Log::info("Direktori Output: {$outputDir}");

        // PERBAIKAN: Mengganti setWorkingDirectory() dengan path()
        $process = Process::timeout($timeout + 60)
            ->path($inputFileDirectory) // <-- Mengatur direktori kerja dengan method yang benar
            ->env(['LD_LIBRARY_PATH' => $qtLibsPath])
            ->run([
                $executablePath,
                '--inputfile='.$inputFileName,
                '--outputdir='.$outputDir,
                '--language=en',
                // '--timelimit-s=' bisa ditambahkan kembali jika diperlukan
            ]);

        if ($process->successful()) {
            Log::info("Engine FET untuk simulasi cluster `{$clusterCode}` berhasil dijalankan.");
        } else {
            Log::error('Proses engine FET untuk simulasi GAGAL.');
            Log::error('FET STDOUT: '.$process->output());
            Log::error('FET STDERR: '.$process->errorOutput());
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::critical('JOB SIMULASI CLUSTER GAGAL PERMANEN: '.$exception->getMessage());
    }
}
