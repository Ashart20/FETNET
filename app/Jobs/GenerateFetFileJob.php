<?php

namespace App\Jobs;

use App\Models\Prodi;
use App\Services\FetFileGeneratorService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\File; // Gunakan facade File untuk operasi direktori
use Throwable;

class GenerateFetFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Waktu maksimal job ini dapat berjalan sebelum timeout (dalam detik).
     * Sebaiknya sedikit lebih lama dari timeout proses FET.
     * @var int
     */
    public int $timeout = 360;

    /**
     * Jumlah percobaan jika job gagal.
     * @var int
     */
    public int $tries = 1;

    /**
     * Konstruktor job.
     *
     * @param Prodi $prodi Model prodi yang akan diproses.
     */
    public function __construct(public Prodi $prodi)
    {
        // Menggunakan property promotion dari PHP 8.0 untuk kode yang lebih ringkas.
    }

    /**
     * Eksekusi job.
     *
     * @param FetFileGeneratorService $generator
     * @return void
     */
    public function handle(FetFileGeneratorService $generator): void
    {
        Log::info("Memulai GenerateFetFileJob untuk Prodi: {$this->prodi->nama_prodi} (ID: {$this->prodi->id})");

        try {
            // Langkah 1: Buat file input .fet
            $inputFilePath = $generator->generateForProdi($this->prodi);
            Log::info("File input .fet berhasil dibuat di: {$inputFilePath}");

            // Langkah 2: Jalankan engine FET-CL
            $result = $this->runFetEngine($inputFilePath);

            // Langkah 3: Proses hasil dari engine FET
            if ($result['process']->successful()) {
                Log::info("Engine FET berhasil dijalankan untuk Prodi: {$this->prodi->nama_prodi}");
                $this->parseFetResults($result['output_dir_path'], $inputFilePath);
            } else {
                Log::error("Proses engine FET GAGAL untuk Prodi: {$this->prodi->nama_prodi}.");
                Log::error("Exit Code: " . $result['process']->exitCode());
                Log::error("Error Output (stderr): " . $result['process']->errorOutput());
                Log::error("Standard Output (stdout): " . $result['process']->output());
            }

        } catch (Exception $e) {
            Log::error("Terjadi error pada GenerateFetFileJob untuk Prodi ID {$this->prodi->id}: " . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }

    /**
     * Menjalankan proses command-line FET.
     *
     * @param string $inputFilePath Path ke file input .fet
     * @return array Mengembalikan hasil proses dan path direktori output
     */
    private function runFetEngine(string $inputFilePath): array
    {
        $fetExecutable = config('fet.executable_path');
        if (empty($fetExecutable) || !file_exists($fetExecutable)) {
            throw new Exception("Path executable FET tidak valid atau tidak ditemukan di: " . $fetExecutable);
        }

        // Membuat direktori output yang unik untuk hasil penjadwalan
        $outputDirName = $this->prodi->kode . '-' . now()->format('Ymd-His');
        $outputDirPath = storage_path('app/fet-results/' . $outputDirName);
        File::makeDirectory($outputDirPath, 0775, true, true);

        $command = [
            $fetExecutable,
            "--inputfile={$inputFilePath}",
            "--outputdir={$outputDirPath}",
            "--language=" . config('fet.language', 'en'),
        ];

        Log::info("Menjalankan command: " . implode(' ', $command));
        $qtLibsPath = '/home/ashart20/fet-7.2.5/usr/lib/'; // Path ke folder libs Qt yang diekstrak

        $env = [
            'LD_LIBRARY_PATH' => $qtLibsPath,
        ];
        $process = Process::timeout(config('fet.timeout', 300))
            ->env($env) // Menambahkan variabel lingkungan di sini
            ->run($command);

        return [
            'process' => $process,
            'output_dir_path' => $outputDirPath,
        ];
    }

    /**
     * Mencari file hasil dan memicu command parser.
     *
     * @param string $outputDirPath Path ke direktori output FET
     * @param string $inputFilePath Path ke file input .fet asli
     * @return void
     */
    private function parseFetResults(string $outputDirPath, string $inputFilePath): void
    {
        // FET membuat sub-direktori dengan nama yang sama dengan file input
        $inputFileBaseName = pathinfo($inputFilePath, PATHINFO_FILENAME);

        // Struktur path file output yang dihasilkan oleh FET-CL
        $resultFile = "{$outputDirPath}/timetables/{$inputFileBaseName}/{$inputFileBaseName}_data_and_timetable.fet";

        if (file_exists($resultFile)) {
            Log::info("File hasil ditemukan. Memicu 'fet:parse' untuk file: {$resultFile}");
            Artisan::call('fet:parse', ['file' => $resultFile]);
            Log::info("Proses parsing untuk Prodi {$this->prodi->nama_prodi} selesai.");
        } else {
            Log::error("File output utama TIDAK DITEMUKAN. Path yang dicari: {$resultFile}");
            // Tambahan: log daftar file di direktori output untuk membantu debug
            $allFiles = File::allFiles($outputDirPath);
            Log::info("File yang ada di direktori output:", array_map(fn($file) => $file->getPathname(), $allFiles));
        }
    }

    /**
     * Menangani kegagalan job.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(Throwable $exception): void
    {

        Log::critical("GenerateFetFileJob untuk Prodi {$this->prodi->nama_prodi} TELAH GAGAL PERMANEN.", [
            'exception_message' => $exception->getMessage(),
        ]);

        // Contoh: kirim notifikasi ke Slack atau email
        // Notification::route('slack', config('services.slack.webhook_url'))
        //     ->notify(new JobFailedNotification($this->prodi, $exception));
    }
}
