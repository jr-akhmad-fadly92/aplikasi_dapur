<?php

namespace App\Exports;

use App\Models\Menu;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\DB;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class laporanPersiapanExport implements FromView, WithColumnWidths, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected $tanggal;

    // Constructor to accept the 'tanggal' variable
    public function __construct($tanggal)
    {
        $this->tanggal = $tanggal;
    }
    public function view(): View
    {
        //
        
        $menu = Menu::with([
            'rincianMenuKarbohidrat.tbPoBahan.tbPenerimaan.warehouseTransaksi' => function ($query) {
                $query->select(
                        DB::raw('SUM(jumlah) as jumlah_masuk'),
                        DB::raw('count(jumlah) as box_masuk'),
                        DB::raw('SUM(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as jumlah_keluar'),
                        DB::raw('COUNT(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as box_keluar'),
                        'id_penerimaan',
                        'warehouse_transaksi.id_satuan', // Mengambil id_satuan untuk LEFT JOIN dengan tabel satuan
                        'satuan.satuan' // Mengambil nama_satuan dari tabel satuan
                    )
                    ->leftJoin('tb_satuan as satuan', 'warehouse_transaksi.id_satuan', '=', 'satuan.id') // LEFT JOIN dengan tabel satuan
                    ->groupBy('id_penerimaan', 'warehouse_transaksi.id_satuan', 'satuan.satuan'); // Kelompokkan berdasarkan id_penerimaan dan id_satuan
            },
            'rincianMenuProtein.tbPoBahan.tbPenerimaan.warehouseTransaksi' => function ($query) {
                $query->select(
                        DB::raw('SUM(jumlah) as jumlah_masuk'),
                        DB::raw('count(jumlah) as box_masuk'),
                        DB::raw('SUM(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as jumlah_keluar'),
                        DB::raw('COUNT(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as box_keluar'),
                        'id_penerimaan',
                        'warehouse_transaksi.id_satuan', // Mengambil id_satuan untuk LEFT JOIN dengan tabel satuan
                        'satuan.satuan' // Mengambil nama_satuan dari tabel satuan
                    )
                    ->leftJoin('tb_satuan as satuan', 'warehouse_transaksi.id_satuan', '=', 'satuan.id') // LEFT JOIN dengan tabel satuan
                    ->groupBy('id_penerimaan', 'warehouse_transaksi.id_satuan', 'satuan.satuan'); // Kelompokkan berdasarkan id_penerimaan dan id_satuan
            },
            'rincianMenuSayur.tbPoBahan.tbPenerimaan.warehouseTransaksi' => function ($query) {
                $query->select(
                        DB::raw('SUM(jumlah) as jumlah_masuk'),
                        DB::raw('count(jumlah) as box_masuk'),
                        DB::raw('SUM(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as jumlah_keluar'),
                        DB::raw('COUNT(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as box_keluar'),
                        'id_penerimaan',
                        'warehouse_transaksi.id_satuan', // Mengambil id_satuan untuk LEFT JOIN dengan tabel satuan
                        'satuan.satuan' // Mengambil nama_satuan dari tabel satuan
                    )
                    ->leftJoin('tb_satuan as satuan', 'warehouse_transaksi.id_satuan', '=', 'satuan.id') // LEFT JOIN dengan tabel satuan
                    ->groupBy('id_penerimaan', 'warehouse_transaksi.id_satuan', 'satuan.satuan'); // Kelompokkan berdasarkan id_penerimaan dan id_satuan
            },
            'rincianMenuSusu.tbPoBahan.tbPenerimaan.warehouseTransaksi' => function ($query) {
                $query->select(
                        DB::raw('SUM(jumlah) as jumlah_masuk'),
                        DB::raw('count(jumlah) as box_masuk'),
                        DB::raw('SUM(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as jumlah_keluar'),
                        DB::raw('COUNT(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as box_keluar'),
                        'id_penerimaan',
                        'warehouse_transaksi.id_satuan', // Mengambil id_satuan untuk LEFT JOIN dengan tabel satuan
                        'satuan.satuan' // Mengambil nama_satuan dari tabel satuan
                    )
                    ->leftJoin('tb_satuan as satuan', 'warehouse_transaksi.id_satuan', '=', 'satuan.id') // LEFT JOIN dengan tabel satuan
                    ->groupBy('id_penerimaan', 'warehouse_transaksi.id_satuan', 'satuan.satuan'); // Kelompokkan berdasarkan id_penerimaan dan id_satuan
            },
            'rincianMenuBuah.tbPoBahan.tbPenerimaan.warehouseTransaksi' => function ($query) {
                $query->select(
                        DB::raw('SUM(jumlah) as jumlah_masuk'),
                        DB::raw('count(jumlah) as box_masuk'),
                        DB::raw('SUM(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as jumlah_keluar'),
                        DB::raw('COUNT(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as box_keluar'),
                        'id_penerimaan',
                        'warehouse_transaksi.id_satuan', // Mengambil id_satuan untuk LEFT JOIN dengan tabel satuan
                        'satuan.satuan' // Mengambil nama_satuan dari tabel satuan
                    )
                    ->leftJoin('tb_satuan as satuan', 'warehouse_transaksi.id_satuan', '=', 'satuan.id') // LEFT JOIN dengan tabel satuan
                    ->groupBy('id_penerimaan', 'warehouse_transaksi.id_satuan', 'satuan.satuan'); // Kelompokkan berdasarkan id_penerimaan dan id_satuan
            },
        ])
        ->where('tanggal_kirim', $this->tanggal)
        ->get();
        
        return view('test.testLaporan', compact('menu'));
    }
    public function styles(Worksheet $sheet)
    {
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'font' => [
                'bold' => false,
            ],
            'alignment' => [
                //'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A1:N2')->getFont()->setBold(true);
        $sheet->getStyle('A1:N2')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:N2')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFCCCCCC'],
            ],
        ]);
        //$sheet->getStyle('A1:L1')->applyFromArray($styleArray); // Mengatur gaya pada header
        $sheet->getStyle('A1:N' . ($sheet->getHighestRow() + 1))->applyFromArray($styleArray); // Mengatur gaya pada seluruh isi data
        $sheet->getStyle('E1:E' . $sheet->getHighestRow())->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('G1:G' . $sheet->getHighestRow())->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('K1:K' . $sheet->getHighestRow())->getNumberFormat()->setFormatCode('#,##0');

    }
    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 16.5,
            'C' => 20,
            'D' => 11,
            'E' => 11,
            'F' => 11,
            'G' => 11,
            'H' => 11,
            'I' => 20,
            'J' => 11,
            'K' => 11,
            'L' => 11,
            'M' => 20,
            'N' => 20,
            'O' => 20,
        ];
    }
}
