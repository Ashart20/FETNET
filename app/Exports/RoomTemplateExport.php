<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RoomTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        array_shift($this->data);
        return $this->data;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'nama_ruangan',
            'kode_ruangan',
            'kode_gedung',
            'lantai',
            'kapasitas',
            'tipe'
        ];
    }
}
