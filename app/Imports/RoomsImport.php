<?php

namespace App\Imports;

use App\Models\Building;
use App\Models\MasterRuangan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;

class RoomsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        // Validasi dasar untuk memastikan kolom yang diperlukan ada
        Validator::make($rows->toArray(), [
            '*.nama_ruangan' => 'required',
            '*.kode_ruangan' => 'required',
            '*.kode_gedung' => 'required',
            '*.lantai' => 'required',
            '*.kapasitas' => 'required|integer',
            '*.tipe' => 'required|string',
        ])->validate();

        foreach ($rows as $row) {
            // LOGIKA UTAMA 1: Cari atau buat Gedung baru secara otomatis
            $building = Building::firstOrCreate(
                [
                    // Cari berdasarkan kode gedung
                    'code' => $row['kode_gedung'],
                ],
                [
                    // Jika tidak ada, buat baru dengan nama & kode yang sama
                    'name' => $row['kode_gedung'],
                ]
            );

            // LOGIKA UTAMA 2: Gunakan ID gedung untuk membuat/memperbarui ruangan
            MasterRuangan::updateOrCreate(
                [
                    // Cari ruangan berdasarkan kodenya
                    'kode_ruangan' => $row['kode_ruangan'],
                ],
                [
                    // Data untuk diupdate atau dibuat
                    'nama_ruangan' => $row['nama_ruangan'],
                    'building_id'  => $building->id, // Gunakan ID dari gedung yang ditemukan/dibuat
                    'lantai'       => $row['lantai'],
                    'kapasitas'    => $row['kapasitas'],
                    'tipe'         => $row['tipe'],
                    'user_id'      => auth()->id(), // Hubungkan dengan user fakultas yang mengimpor
                ]
            );
        }
    }
}
