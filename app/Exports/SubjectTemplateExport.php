<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SubjectTemplateExport implements FromCollection, WithHeadings
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {

        return new Collection($this->data);
    }

    /**
     * @return array
     */
    public function headings(): array
    {

        return ['nama_matkul', 'kode_matkul', 'sks'];
    }
}
