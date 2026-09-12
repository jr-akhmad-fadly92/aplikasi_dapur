<?php

namespace App\Exports;

use App\Models\DataDapur;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanBiayaSewaExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $data2;
    protected $start;
    protected $end;

    public function __construct( $data2, $start, $end)
    {
        $this->data2  = $data2;
        $this->start = $start;
        $this->end   = $end;
    }

    public function view(): View
    {
        $dapur = DataDapur::first();
        return view('exports.laporan_biaya_Sewa', [
            
            'data2'  => $this->data2,
            'start' => $this->start,
            'end'   => $this->end,
            'dapur' => $dapur
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Hitung jumlah baris
       
        $lastRow2 = count($this->data2) + 6; // 6 = header + judul
        $jumlah_row =$lastRow2;
        return [
            // Header tebal
            4    => ['font' => ['bold' => true]],

            // Border semua tabel
            "A10:G13" => [
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


