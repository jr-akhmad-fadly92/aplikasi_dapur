<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;

class LaporanBahanExport implements FromView, ShouldAutoSize
{
    use Exportable;

    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        // Query utama
        $query = DB::table('tb_po_bahan')
            ->join('tb_po', 'tb_po_bahan.id_po', '=', 'tb_po.id')
            ->join('tb_master_bahan', 'tb_po_bahan.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'tb_po_bahan.satuan', '=', 'tb_satuan.id') // JOIN SATUAN
            ->whereNotNull('tb_po.tanggal_approve')
            ->select([
                'tb_po.nomor_po',
                DB::raw('SUM(tb_po_bahan.jumlah_bahan) AS total_jumlah_bahan'),
                DB::raw('MIN(tb_po_bahan.jumlah_po) AS jumlah_po'),
                DB::raw('MIN(tb_po.tanggal_approve) AS tanggal_approve'),
                DB::raw('MIN(tb_master_bahan.bahan) AS bahan')
                , 'tb_satuan.satuan' // TAMBAHAN
            ])
            ->groupBy('tb_po.nomor_po');

        // Filter tanggal jika ada
        if ($this->startDate && $this->endDate) {
            try {
                $startDateFormatted = Carbon::createFromFormat('d/m/Y', $this->startDate)->format('Y-m-d');
                $endDateFormatted   = Carbon::createFromFormat('d/m/Y', $this->endDate)->format('Y-m-d');

                $query->whereBetween('tb_po.tanggal_approve', [$startDateFormatted, $endDateFormatted]);
            } catch (\Exception $e) {
                // Jika tanggal salah, skip filter
            }
        }

        // Eksekusi query (ORDERING sesuai tanggal)
        $data = $query->orderBy('tanggal_approve', 'asc')->get();

        // Format data untuk view Excel
        $data = $data->map(function ($item, $index) {
            $item->row_number = $index + 1;
            $item->tanggal_formatted = Carbon::parse($item->tanggal_approve)->format('d/m/Y');
            $item->jumlah_formatted = number_format($item->total_jumlah_bahan, 0, ',', '.');
            $item->harga_formatted  = 'Rp ' . number_format($item->jumlah_po, 0, ',', '.');
            return $item;
        });

        // Total harga keseluruhan
        $totalHarga = $data->sum('jumlah_po');

        return view('office.laporanbahan.excel.export-excel-optimized', [
            'data' => $data,
            'totalHarga' => $totalHarga,
            'startDate' => $this->startDate ?? null,
            'endDate' => $this->endDate ?? null,
            'totalRecords' => $data->count()
        ]);
    }

}
