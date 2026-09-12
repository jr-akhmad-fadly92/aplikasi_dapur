<?php

namespace App\Exports;

use App\Models\TbMasterBahan;
use App\Models\TbHargaHet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HargaHetTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        $downloadDate = date('Y-m-d');
        $bahanList = TbMasterBahan::orderBy('bahan', 'asc')->get();
        $rows = [];

        foreach ($bahanList as $bahan) {
            $hargaHet = TbHargaHet::where('id_bahan', $bahan->id)->first();

            $rows[] = [
                $bahan->id,
                $bahan->bahan,
                $downloadDate,
                $hargaHet ? (int) $hargaHet->harga_het : 0,
            ];
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'id_bahan',
            'nama_bahan',
            'tanggal_update',
            'harga_het',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1F4E78'],
                ],
            ],
        ];
    }
}
