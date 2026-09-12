<?php

namespace App\Exports;

use App\Models\TbPoBahan;
use App\Models\TbMasterBahan;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\DB;

class LaporanBahanBakuExport implements FromView, ShouldAutoSize, WithStyles, WithEvents
{
    use Exportable;

    protected $startDate;
    protected $endDate;
    protected $jenis;

    public function __construct($startDate = null, $endDate = null, $jenis = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->jenis = $jenis;
    }

    public function view(): View
    {
        // Query untuk mengambil data laporan bahan baku
        $query = DB::table('tb_po_bahan')
        ->join('tb_po', 'tb_po_bahan.id_po', '=', 'tb_po.id')
        ->join('tb_master_bahan', 'tb_po_bahan.id_bahan', '=', 'tb_master_bahan.id')
        ->join('tb_satuan', 'tb_po_bahan.satuan', '=', 'tb_satuan.id') // JOIN SATUAN
        ->whereNotNull('tb_po.tanggal_approve')
        ->select([
            'tb_master_bahan.bahan as nama_bahan',
            'tb_master_bahan.jenis',
            'tb_master_bahan.id',
            'tb_satuan.satuan', // TAMBAHAN
            DB::raw('SUM(tb_po_bahan.jumlah_bahan) as total_bahan'),
            'tb_po.tanggal_approve',
            'tb_po.nomor_po'
        ])
        ->groupBy(
            'tb_master_bahan.id',
            'tb_master_bahan.bahan',
            'tb_master_bahan.jenis',
            'tb_satuan.satuan', // WAJIB groupBy
            'tb_po.tanggal_approve',
            'tb_po.nomor_po'
        );

        // Apply filters
        if (!empty($this->jenis)) {
            $query->where('tb_master_bahan.id', $this->jenis);
        }

        if (!empty($this->startDate) && !empty($this->endDate)) {
            try {
                $startDateFormatted = Carbon::createFromFormat('d/m/Y', $this->startDate)->format('Y-m-d');
                $endDateFormatted = Carbon::createFromFormat('d/m/Y', $this->endDate)->format('Y-m-d');
                $query->whereBetween('tb_po.tanggal_approve', [$startDateFormatted, $endDateFormatted]);
            } catch (\Exception $e) {
                // Handle invalid date format
            }
        }

        $data = $query->orderBy('tb_po.tanggal_approve', 'desc')
                 ->orderBy('tb_master_bahan.bahan', 'asc')
                 ->get();

        // Map jenis untuk display
        $jenisMap = [
            1 => 'Beras',
            2 => 'Lauk',
            3 => 'Sayur', 
            4 => 'Buah',
            5 => 'Suplemen',
            6 => 'Bumbu',
            7 => 'Penunjang'
        ];

        $data = $data->map(function($item) use ($jenisMap) {
            $item->jenis_nama = $jenisMap[$item->jenis] ?? 'Tidak Diketahui';
            $item->tanggal_formatted = Carbon::parse($item->tanggal_approve)->format('d/m/Y');
            return $item;
        });

        $title = 'Laporan Bahan Baku';
        $filterInfo = [];
        
        if (!empty($this->startDate) && !empty($this->endDate)) {
            $filterInfo[] = "Periode: {$this->startDate} - {$this->endDate}";
        }
        
        if (!empty($this->jenis)) {
            $filterInfo[] = "Jenis: " . ($jenisMap[$this->jenis] ?? 'Tidak Diketahui');
        }

        return view('office.laporanbahanbaku.excel.export-excel', [
            'data' => $data,
            'title' => $title,
            'filterInfo' => $filterInfo,
            'totalRecords' => $data->count(),
            'totalBahan' => $data->sum('total_bahan')
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as header
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 16
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ]
            ],
            // Style header table
            4 => [
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FFE2E2E2',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Add borders to all data
                $cellRange = 'A4:F' . ($event->sheet->getHighestRow());
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Merge cells for title
                $event->sheet->getDelegate()->mergeCells('A1:F1');
                $event->sheet->getDelegate()->mergeCells('A2:F2');
            },
        ];
    }
}
