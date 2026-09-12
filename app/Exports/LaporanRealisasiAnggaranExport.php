<?php

namespace App\Exports;

use App\Models\DataDapur;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanRealisasiAnggaranExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $data_anggaran;
    protected $totalJumlahPoPangan;
    protected $jumlah_non_pangan;
    protected $jumlah_infra;
    protected $total_pengeluaran;
    protected $saldo;

    protected $start;
    protected $end;

    public function __construct($data_anggaran, $totalJumlahPoPangan, $jumlah_non_pangan, $jumlah_infra, $total_pengeluaran, $saldo)
    {
        $this->data_anggaran     = $data_anggaran;
        $this->totalJumlahPoPangan = $totalJumlahPoPangan;
        $this->jumlah_non_pangan = $jumlah_non_pangan;
        $this->jumlah_infra      = $jumlah_infra;
        $this->total_pengeluaran = $total_pengeluaran;
        $this->saldo             = $saldo;

        $this->start = $data_anggaran->periode_awal;
        $this->end   = $data_anggaran->periode_akhir;
    }

    public function view(): View
    {
        $dapur = DataDapur::first();
        return view('exports.laporan_realisasi_anggaran', [
            'data_anggaran'       => $this->data_anggaran,
            'totalJumlahPoPangan' => $this->totalJumlahPoPangan,
            'jumlah_non_pangan'   => $this->jumlah_non_pangan,
            'jumlah_infra'        => $this->jumlah_infra,
            'total_pengeluaran'   => $this->total_pengeluaran,
            'saldo'               => $this->saldo,
            'start'               => $this->start,
            'end'                 => $this->end,
            'dapur'               => $dapur,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow2   = 6; // 6 = header + judul
        $jumlah_row = $lastRow2;

        return [
            // Header tebal
            4 => ['font' => ['bold' => true]],

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



