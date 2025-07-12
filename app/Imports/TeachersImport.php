<?php

namespace App\Imports;

use App\Models\Teacher;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class TeachersImport implements ToCollection, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    private $prodiId;

    public function __construct(int $prodiId)
    {
        $this->prodiId = $prodiId;
    }

    public function collection(Collection $rows)
    {


        foreach ($rows as $row) {

            if (empty($row['kode_dosen']) || empty($row['nama_dosen'])) {
                continue;
            }

            $teacher = Teacher::updateOrCreate(
                [

                    'kode_dosen' => $row['kode_dosen'],
                ],
                [
                    // Data yang akan diisi atau diperbarui
                    'nama_dosen'     => $row['nama_dosen'],
                    'title_depan'    => $row['title_depan'] ?? null,
                    'title_belakang' => $row['title_belakang'] ?? null,
                    'kode_univ'      => $row['kode_univ'] ?? null,
                    'employee_id'    => $row['employee_id'] ?? null,
                    'email'          => $row['email'] ?? null,
                    'nomor_hp'       => $row['nomor_hp'] ?? null,
                ]
            );

            // Tautkan dosen dengan prodi pengguna yang mengimpor
            if ($teacher) {
                $teacher->prodis()->syncWithoutDetaching($this->prodiId);
            }
        }
    }
}
