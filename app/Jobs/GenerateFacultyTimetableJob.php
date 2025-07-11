<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Schedule;
use App\Services\FetFileGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\DB;
use Throwable;

class GenerateFacultyTimetableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1800;
    public int $tries = 1;
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle(FetFileGeneratorService $fetFileGenerator): void
    {
        Log::info('MEMULAI PROSES GENERATE JADWAL FAKULTAS (GABUNGAN)');
        Log::info('Menghapus semua data jadwal lama...');
        DB::table('schedule_teacher')->delete();
        Schedule::query()->delete();
        Log::info('Data jadwal lama berhasil dihapus.');

        try {
            // PANGGILAN TUNGGAL: Panggil service untuk membuat satu file .fet untuk seluruh fakultas
            $inputFilePath = $fetFileGenerator->generateForFaculty();
            Log::info("File input .fet gabungan berhasil dibuat di: {$inputFilePath}");

            // Jalankan FET Engine sekali untuk file gabungan
            $this->runFetEngine($inputFilePath);

        } catch (\Exception $e) {
            Log::error("GAGAL TOTAL DI TENGAH PROSES GENERATE FAKULTAS: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            // Jika gagal, job akan berhenti dan ditandai sebagai failed().
            throw $e;
        }

        Log::info('PROSES GENERATE JADWAL FAKULTAS (GABUNGAN) SELESAI.');
    }

    private function runFetEngine(string $inputFilePath): void
    {
        $executablePath = config('fet.executable_path');
        $qtLibsPath = config('fet.qt_library_path');
        $timeout = config('fet.timeout', 1800); // Ambil timeout dari config

        // Output disimpan di direktori 'fakultas'
        $outputDir = storage_path("app/fet-results/fakultas");

        File::ensureDirectoryExists($outputDir);

        Log::info("Menggunakan FET executable dari: {$executablePath}");
        Log::info("Menggunakan QT Library Path: {$qtLibsPath}");
        Log::info("Timeout set ke: {$timeout} detik.");

        $process = Process::timeout($timeout + 60)
            ->env(['LD_LIBRARY_PATH' => $qtLibsPath])
            ->run([
                $executablePath,
                '--inputfile=' . $inputFilePath,
                '--outputdir=' . $outputDir,
                '--language=en',
                '--timelimit-s=' . $timeout
            ]);

        if ($process->successful()) {
            Log::info("Engine FET berhasil dijalankan untuk file gabungan.");

            $inputFileNameWithoutExt = pathinfo($inputFilePath, PATHINFO_FILENAME);
            $outputSubdirectory = "{$outputDir}/timetables/{$inputFileNameWithoutExt}";
            $outputFileName = "{$inputFileNameWithoutExt}_data_and_timetable.fet";
            $outputFilePath = "{$outputSubdirectory}/{$outputFileName}";

            if (File::exists($outputFilePath)) {
                Log::info("File hasil ditemukan, memanggil parser: {$outputFilePath}");
                Artisan::call('fet:parse', [
                    'file' => $outputFilePath,
                    '--no-cleanup' => true
                ]);
                Log::info("Parsing untuk jadwal fakultas selesai.");
            } else {
                Log::error("Parsing GAGAL: File hasil tidak ditemukan di path yang diharapkan: {$outputFilePath}");
            }
        } else {
            Log::error("Proses engine FET GAGAL.");
            Log::error("FET STDOUT: " . $process->output());
            Log::error("FET STDERR: " . $process->errorOutput());
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::critical("JOB GENERATE FACULTY TIMETABLE GAGAL PERMANEN: " . $exception->getMessage());
    }
}
