<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Prodi;
use App\Services\FetFileGeneratorService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Throwable;

class GenerateFacultyTimetableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600;
    public int $tries = 1;
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle(FetFileGeneratorService $fetFileGenerator): void
    {
        $prodis = Prodi::all();

        foreach ($prodis as $prodi) {
            try {
                Log::info("[Prodi: {$prodi->kode}] Memulai proses generate.");

                $inputFilePath = $fetFileGenerator->generateForProdi($prodi);
                Log::info("[Prodi: {$prodi->kode}] File input .fet berhasil dibuat di: {$inputFilePath}");

                $this->runFetEngine($inputFilePath, $prodi);

            } catch (\Exception $e) {
                Log::error("[Prodi: {$prodi->kode}] Gagal total di tengah proses: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                continue;
            }
        }
    }

    private function runFetEngine(string $inputFilePath, Prodi $prodi): void
    {
        $executablePath = config('fet.executable_path');
        $qtLibsPath = config('fet.qt_library_path');
        $timeout = config('fet.timeout');
        $outputDir = storage_path("app/fet-results/{$prodi->kode}");

        File::ensureDirectoryExists($outputDir);

        Log::info("[Prodi: {$prodi->kode}] Menggunakan FET executable dari: {$executablePath}");
        Log::info("[Prodi: {$prodi->kode}] Menggunakan QT Library Path: {$qtLibsPath}");

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
            Log::info("[Prodi: {$prodi->kode}] Engine FET berhasil dijalankan.");

            // [PERBAIKAN UTAMA] Tentukan path ke file HASIL yang benar, dengan memperhitungkan subfolder
            $inputFileNameWithoutExt = pathinfo($inputFilePath, PATHINFO_FILENAME); // cth: input_prodi_E3434_1752134779

            $outputSubdirectory = "{$outputDir}/timetables/{$inputFileNameWithoutExt}";
            $outputFileName = "{$inputFileNameWithoutExt}_data_and_timetable.fet";
            $outputFilePath = "{$outputSubdirectory}/{$outputFileName}";

            if (File::exists($outputFilePath)) {
                Log::info("[Prodi: {$prodi->kode}] File hasil ditemukan, memanggil parser: {$outputFilePath}");
                Artisan::call('fet:parse', ['file' => $outputFilePath]);
                Log::info("[Prodi: {$prodi->kode}] Parsing selesai.");
            } else {
                Log::error("[Prodi: {$prodi->kode}] Parsing GAGAL: File hasil tidak ditemukan di path yang diharapkan: {$outputFilePath}");
            }
        } else {
            Log::error("[Prodi: {$prodi->kode}] Proses engine FET GAGAL.");
            Log::error("[Prodi: {$prodi->kode}] FET STDOUT: " . $process->output());
            Log::error("[Prodi: {$prodi->kode}] FET STDERR: " . $process->errorOutput());
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::critical("Job GenerateFacultyTimetableJob GAGAL PERMANEN: " . $exception->getMessage());
    }
}
