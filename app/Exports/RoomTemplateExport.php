<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RoomTemplateExport implements FromArray, ShouldAutoSize, WithHeadings
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        array_shift($this->data);

        return $this->data;
    }

    public function headings(): array
    {
        return [
            'nama_ruangan',
            'kode_ruangan',
            'kode_gedung',
            'lantai',
            'kapasitas',
            'tipe',
        ];
    }
}
