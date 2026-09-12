<?php

namespace App\Exports;

use App\Models\DataDapur;
use App\Models\TbPoBahan;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class FormStokOpnamNonPanganExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $start;
    protected $count;

    public function __construct( $data, $start, $count)
    {
        $this->data  = $data;
        $this->start = $start;
        $this->count  = $count;
    }

    public function view(): View
    {
        $dapur = DataDapur::first();
        $data_non_pangan  = DB::table('tb_po_bahan as pb')
            ->join('tb_master_bahan as mb', 'pb.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as s', 'pb.satuan', '=', 's.id')
            ->select(
                'mb.bahan',
                'pb.jumlah_bahan as total_jumlah',
                's.satuan as nama_satuan'
            )
            ->whereBetween('pb.tanggal_kedatangan', [
            $this->start . ' 00:00:00',
            $this->start . ' 23:59:59'
            ])
            ->get();
        $rows = $this->count ?? count($this->data) ?? 30;

        return view('exports.Form_stock_opnam_non_pangan_kosong', [
            'data'  => $this->data,
            'data_non_pangan'  => $data_non_pangan,
            'count'  => $this->count,
            'start' => $this->start,
            'dapur' => $dapur,
            'rows'  => $rows,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Hitung jumlah baris
        $rows = $this->count ?? count($this->data) ?? 30;
        $jumlah_row = $rows + 5; // offset header (baris awal sampai tabel mulai)
        return [
            // Header tebal
            4    => ['font' => ['bold' => true]],

            // Border semua tabel
            "B5:I{$jumlah_row}" => [
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


