<?php

namespace App\Jobs;

use App\Models\Schedule;
use App\Models\User;
use App\Services\FetFileGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Throwable;

class GenerateFacultyTimetableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * Menyimpan ID user yang memulai proses ini.
     * @var int
     */
    public int $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public int $timeout = 1800;

    public int $tries = 1;

    protected $user;


    public function handle(FetFileGeneratorService $fetFileGenerator): void
    {
        Log::info('MEMULAI PROSES GENERATE JADWAL FAKULTAS (GABUNGAN)');

        try {
            // <-- `try` dimulai dari sini, membungkus semua proses

            // Langkah 1: Hapus jadwal lama
            Log::info('Menghapus semua data jadwal lama...');
            DB::table('schedule_teacher')->delete();
            Schedule::query()->delete();
            Log::info('Data jadwal lama berhasil dihapus.');

            // Langkah 2: Buat file input .fet
            $inputFilePath = $fetFileGenerator->generateForFaculty(null, $this->userId);
            Log::info("File input .fet gabungan berhasil dibuat di: {$inputFilePath}");

            // Langkah 3: Jalankan FET Engine dan proses hasilnya
            // (Asumsi method ini juga bisa melempar Exception jika gagal)
            $this->runFetEngine($inputFilePath);

            Log::info('--- PROSES GENERATE JADWAL FAKULTAS SELESAI DENGAN SUKSES ---');

        } catch (\Exception $e) {
            // Jika ada error di langkah mana pun, akan ditangkap di sini
            Log::error('!!! PROSES GENERATE JADWAL GAGAL !!!');
            Log::error('Pesan Error: ' . $e->getMessage());
            Log::error('Lokasi File: ' . $e->getFile() . ' pada baris ' . $e->getLine());
            Log::error('Stack Trace: ' . substr($e->getTraceAsString(), 0, 2000)); // Dibatasi agar log tidak terlalu panjang

            // Melempar kembali exception agar job ditandai sebagai 'failed' oleh Laravel
            throw $e;
        }
    }

    private function runFetEngine(string $inputFilePath): void
    {
        $executablePath = config('fet.executable_path');
        $qtLibsPath = config('fet.qt_library_path');
        $timeout = config('fet.timeout', 1800); // Ambil timeout dari config

        // Output disimpan di direktori 'fakultas'
        $outputDir = storage_path('app/fet-results/fakultas');

        File::ensureDirectoryExists($outputDir);

        Log::info("Menggunakan FET executable dari: {$executablePath}");
        Log::info("Menggunakan QT Library Path: {$qtLibsPath}");
        Log::info("Timeout set ke: {$timeout} detik.");

        $process = Process::timeout($timeout + 60)
            ->env(['LD_LIBRARY_PATH' => $qtLibsPath])
            ->run([
                $executablePath,
                '--inputfile='.$inputFilePath,
                '--outputdir='.$outputDir,
                '--language=en',
                '--timelimitseconds='.$timeout,
            ]);

        if ($process->successful()) {
            Log::info('Engine FET berhasil dijalankan untuk file gabungan.');

            $inputFileNameWithoutExt = pathinfo($inputFilePath, PATHINFO_FILENAME);
            $outputSubdirectory = "{$outputDir}/timetables/{$inputFileNameWithoutExt}";
            $outputFileName = "{$inputFileNameWithoutExt}_data_and_timetable.fet";
            $outputFilePath = "{$outputSubdirectory}/{$outputFileName}";

            if (File::exists($outputFilePath)) {
                Log::info("File hasil ditemukan, memanggil parser: {$outputFilePath}");
                Artisan::call('fet:parse', [
                    'file' => $outputFilePath,
                    '--no-cleanup' => true,
                ]);
                Log::info('Parsing untuk jadwal fakultas selesai.');
            } else {
                Log::error("Parsing GAGAL: File hasil tidak ditemukan di path yang diharapkan: {$outputFilePath}");
            }
        } else {
            Log::error('Proses engine FET GAGAL.');
            Log::error('FET STDOUT: '.$process->output());
            Log::error('FET STDERR: '.$process->errorOutput());
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::critical('JOB GENERATE FACULTY TIMETABLE GAGAL PERMANEN: '.$exception->getMessage());
    }
}
