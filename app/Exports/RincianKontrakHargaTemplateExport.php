<?php

namespace App\Exports;

use App\Models\TbMasterBahan;
use App\Models\TbRincianKontrak;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RincianKontrakHargaTemplateExport implements FromArray, WithHeadings, WithStyles
{
    protected $idKontrak;

    public function __construct(int $idKontrak)
    {
        $this->idKontrak = $idKontrak;
    }

    public function array(): array
    {
        $rows = TbRincianKontrak::where('id_kontrak', $this->idKontrak)
            ->where('status', 1)
            ->orderBy('id', 'asc')
            ->get();

        $result = [];

        foreach ($rows as $row) {
            $namaBahan = '-';
            if (!empty($row->id_bahan)) {
                $bahan = TbMasterBahan::find($row->id_bahan);
                if ($bahan) {
                    $namaBahan = $bahan->bahan;
                }
            }

            $result[] = [
                $row->id,
                $row->id_kontrak,
                $row->id_bahan,
                $namaBahan,
                (int) $row->harga_bahan,
                (int) $row->harga_bahan,
            ];
        }

        return $result;
    }

    public function headings(): array
    {
        return [
            'id_rincian_kontrak',
            'id_kontrak',
            'id_bahan',
            'nama_bahan',
            'harga_lama',
            'harga_baru',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        // Header kolom harga_baru dibuat hijau agar jelas ini kolom yang diedit.
        $sheet->getStyle('F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E7D32'],
            ],
        ]);

        // Isi kolom harga_baru diberi warna hijau muda.
        if ($highestRow >= 2) {
            $sheet->getStyle('F2:F' . $highestRow)->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E8F5E9'],
                ],
            ]);
        }

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
