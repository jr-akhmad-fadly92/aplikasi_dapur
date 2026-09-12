<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\DataDapur;


class LaporanHarianDapurExport implements FromView, ShouldAutoSize, WithStyles, WithEvents
{
    protected $all_data;
    protected $rows;

    public function __construct($all_data = [], $rows = 30)
    {
        $this->all_data = $all_data;
        $this->rows = $rows;
     
    }

    public function view(): View
    {
        $dapur = Datadapur::first();
        return view('exports.laporan_harian_dapur', [
            'all_data' => $this->all_data,
            'dapur' => $dapur,
            'rows' => $this->rows,
          
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Hitung total baris berdasarkan data
        $headerRows = 6; // Baris 1-6 adalah header
        $totalDataRows = 0;
        
        foreach ($this->all_data as $item) {
            $jumlahHari = $item['data_anggaran']->jumlah_hari ?? count($item['tanggalList']);
            // Jika lebih dari 6 hari, butuh 4 baris, jika tidak butuh 2 baris
            $totalDataRows += ($jumlahHari > 6) ? 20 : 10;
        }
        
        $lastRow = $headerRows + $totalDataRows;
        $lastColumn = 'N'; // Kolom terakhir (A sampai N = 11 kolom)
        
        return [
            1 => ['font' => ['bold' => true]],
            // Border untuk semua data dari B6 sampai kolom terakhir dan baris terakhir
            "B6:{$lastColumn}{$lastRow}" => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Register events to tweak styles after the sheet is generated.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $this->rows + 5;

                // Search for the specific date text and force a green fill.
                // Trim values when comparing to avoid trailing-space mismatches.
                for ($row = 1; $row <= $lastRow; $row++) {
                    for ($col = 1; $col <= 6; $col++) {
                        $cell = $sheet->getCellByColumnAndRow($col, $row);
                        $value = trim((string) $cell->getValue());
                        if ($value === '7 Juli - 11 Juli 2025') {
                            $coord = $cell->getCoordinate();
                            $sheet->getStyle($coord)->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('FF00B050');
                        }
                    }
                }
            },
        ];
    }
}
