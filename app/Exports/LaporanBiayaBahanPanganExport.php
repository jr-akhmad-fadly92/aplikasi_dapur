<?php

namespace App\Exports;

use App\Models\DataDapur;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanBiayaBahanPanganExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $start;
    protected $end;

    public function __construct($data, $start, $end)
    {
        $this->data  = $data;
        $this->start = $start;
        $this->end   = $end;
    }

    public function view(): View
    {
        $dapur = DataDapur::first();
        return view('exports.laporan_biaya_bahan_pangan', [
            'data'  => $this->data,
            'start' => $this->start,
            'end'   => $this->end,
            'dapur' => $dapur
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Hitung jumlah baris
        $lastRow = count($this->data) + 14; // 6 = header + judul

        return [
            // Header tebal
            4    => ['font' => ['bold' => true]],

            // Border semua tabel
            "A10:I{$lastRow}" => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color'       => ['argb' => '000000'],
                    ],
                ],
            ],
        ];
    }
}
