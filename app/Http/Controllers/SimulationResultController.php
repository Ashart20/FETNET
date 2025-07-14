<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class SimulationResultController extends Controller
{
    public function show(string $simulation_folder, string $file_name): SymfonyResponse
    {
        // --- Langkah Debug 1: Log informasi awal ---
        Log::info('--- DEBUGGING SimulationResultController ---');
        Log::info("Mencoba menampilkan file: {$file_name} dari folder: {$simulation_folder}");

        $user = Auth::user();
        if (! $user || ! $user->cluster) {
            Log::error('DEBUG: Gagal karena user tidak login atau tidak memiliki cluster.');
            abort(403, 'Akses ditolak.');
        }

        $clusterCode = $user->cluster->code;
        Log::info("DEBUG: Kode Cluster terdeteksi: {$clusterCode}");

        // --- Langkah Debug 2: Bangun dan log path lengkap ---
        $path = storage_path("app/public/simulations/{$clusterCode}/timetables/{$simulation_folder}/{$file_name}");
        Log::info("DEBUG: Path lengkap yang sedang diperiksa: {$path}");

        // --- Langkah Debug 3: Cek apakah file benar-benar ada ---
        if (! File::exists($path)) {
            Log::error('DEBUG: File::exists() mengembalikan FALSE. File tidak ditemukan di path tersebut.');
            abort(404, 'File simulasi tidak ditemukan. Path yang dicari: '.$path);
        }

        // --- Jika berhasil, lanjutkan ---
        Log::info('DEBUG: File::exists() mengembalikan TRUE. File ditemukan, mencoba menyajikan...');

        $fileContent = File::get($path);
        $mimeType = File::mimeType($path);

        return Response::make($fileContent, 200)->header('Content-Type', $mimeType);
    }
}
