<?php

namespace App\Exports;

use App\Models\DataDapur;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanBiayaNonPanganExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $data2;
    protected $start;
    protected $end;

    public function __construct($data, $data2, $start, $end)
    {
        $this->data  = $data;
        $this->data2  = $data2;
        $this->start = $start;
        $this->end   = $end;
    }

    public function view(): View
    {
        $dapur = DataDapur::first();
        return view('exports.laporan_biaya_non_pangan', [
            'data'  => $this->data,
            'data2'  => $this->data2,
            'start' => $this->start,
            'end'   => $this->end,
            'dapur' => $dapur
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Hitung jumlah baris
        $lastRow = count($this->data) + 12; // 6 = header + judul
        $lastRow2 = count($this->data2) ; // 6 = header + judul
        $jumlah_row = $lastRow+ $lastRow2;
        return [
            // Header tebal
            4    => ['font' => ['bold' => true]],

            // Border semua tabel
            "A10:G{$jumlah_row}" => [
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

