<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TeacherTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Mengembalikan data array (tanpa header).
     * @return array
     */
    public function array(): array
    {
        // Menghapus baris header dari data
        array_shift($this->data);
        return $this->data;
    }

    /**
     * Mendefinisikan header untuk file Excel.
     * @return array
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
            'nomor_hp'
        ];
    }
}
