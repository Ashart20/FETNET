<?php

namespace App\Imports;

use App\Models\Subject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubjectsImport implements ToCollection, WithHeadingRow
{
    private int $prodiId;

    public function __construct(int $prodiId)
    {
        $this->prodiId = $prodiId;
    }

    public function collection(Collection $rows)
    {
        Validator::make($rows->toArray(), [
            '*.nama_matkul' => ['required', 'string'],
            '*.kode_matkul' => ['required', 'string'],
            '*.sks' => ['required', 'integer'],
        ])->validate();

        foreach ($rows as $row) {
            Subject::firstOrCreate(
                [
                    // Kondisi untuk mencari:
                    'prodi_id' => $this->prodiId,
                    'kode_matkul' => $row['kode_matkul'],
                ],
                [
                    'nama_matkul' => $row['nama_matkul'],
                    // Data ini HANYA akan digunakan jika record BARU dibuat:
                    'sks' => $row['sks'],
                    'semester' => $row['semester'],
                ]
            );
        }
    }
}
