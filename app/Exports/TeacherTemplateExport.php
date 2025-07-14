<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TeacherTemplateExport implements FromArray, ShouldAutoSize, WithHeadings
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Mengembalikan data array (tanpa header).
     */
    public function array(): array
    {
        // Menghapus baris header dari data
        array_shift($this->data);

        return $this->data;
    }

    /**
     * Mendefinisikan header untuk file Excel.
     */
    public function headings(): array
    {
        // Definisikan header secara eksplisit
        return [
            'nama_dosen',
            'kode_dosen',
            'title_depan',
            'title_belakang',
            'kode_univ',
            'employee_id',
            'email',
            'nomor_hp',
        ];
    }
}
