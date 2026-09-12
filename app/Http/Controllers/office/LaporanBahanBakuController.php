<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TbPoBahan;
use App\Models\TbMasterBahan;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\LaporanBahanBakuExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class LaporanBahanBakuController extends Controller
{
    public function index()
    {
        $header = "Laporan Bahan Baku";
        $master_bahan =  DB::table('tb_master_bahan')
                        ->join('tb_po_bahan', 'tb_master_bahan.id', '=', 'tb_po_bahan.id_bahan')
                        ->select(
                            'tb_master_bahan.id',
                            'tb_master_bahan.bahan'
                        )
                        ->distinct()
                        ->orderBy('tb_master_bahan.bahan', 'asc')
                        ->get();
        return view('office.laporanbahanbaku.index', compact('header', 'master_bahan'));
    }

    public function getData(Request $request)
    {
        // Query untuk mengambil data laporan bahan baku yang dipesan
        $query = DB::table('tb_po_bahan')
            ->join('tb_po', 'tb_po_bahan.id_po', '=', 'tb_po.id')
            ->join('tb_master_bahan', 'tb_po_bahan.id_bahan', '=', 'tb_master_bahan.id')
            ->whereNotNull('tb_po.tanggal_approve')
            ->select([
                'tb_master_bahan.bahan as nama_bahan',
                'tb_master_bahan.jenis',
                'tb_master_bahan.id',
                DB::raw('SUM(tb_po_bahan.jumlah_bahan) as total_bahan'),
                'tb_po.tanggal_approve',
                'tb_po.nomor_po'
            ])
            ->groupBy('tb_master_bahan.id', 'tb_master_bahan.bahan', 'tb_master_bahan.jenis', 'tb_po.tanggal_approve', 'tb_po.nomor_po');

        // Filter berdasarkan jenis bahan jika ada
        if ($request->has('jenis') && !empty($request->jenis)) {
            $query->where('tb_master_bahan.id', $request->jenis);
        }

        // Filter berdasarkan tanggal jika ada
        if (
            $request->has('start_date') && $request->has('end_date') &&
            !empty($request->start_date) && !empty($request->end_date)
        ) {
            try {
                $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
                $endDate = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');

                $query->whereBetween('tb_po.tanggal_approve', [$startDate, $endDate]);
            } catch (\Exception $e) {
                // Jika format tanggal salah, tidak apply filter
            }
        }

        // Default ordering
        $query = $query->orderBy('tb_po.tanggal_approve', 'desc')
            ->orderBy('tb_master_bahan.bahan', 'asc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($row) {
                return $row->tanggal_approve ? Carbon::parse($row->tanggal_approve)->format('d/m/Y') : '-';
            })
            ->addColumn('nomor_po', function ($row) {
                return $row->nomor_po ?? '-';
            })
            ->addColumn('nama_bahan', function ($row) {
                return $row->nama_bahan ?? '-';
            })
            ->addColumn('jenis_bahan', function ($row) {
                $jenisMap = [
                    1 => 'Beras',
                    2 => 'Lauk',
                    3 => 'Sayur',
                    4 => 'Buah',
                    5 => 'Suplemen',
                    6 => 'Bumbu',
                    7 => 'Penunjang'
                ];
                return $jenisMap[$row->jenis] ?? 'Tidak Diketahui';
            })
            ->addColumn('total_bahan', function ($row) {
                return number_format($row->total_bahan, 0, ',', '.');
            })
            ->rawColumns(['tanggal', 'nomor_po', 'nama_bahan', 'jenis_bahan', 'total_bahan'])
            ->make(true);
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $jenis = $request->jenis;

        // Query untuk export PDF
        $query = DB::table('tb_po_bahan')
            ->join('tb_po', 'tb_po_bahan.id_po', '=', 'tb_po.id')
            ->join('tb_master_bahan', 'tb_po_bahan.id_bahan', '=', 'tb_master_bahan.id')
            ->whereNotNull('tb_po.tanggal_approve')
            ->select([
                'tb_master_bahan.bahan as nama_bahan',
                'tb_master_bahan.jenis',
                'tb_master_bahan.id',
                DB::raw('SUM(tb_po_bahan.jumlah_bahan) as total_bahan'),
                'tb_po.tanggal_approve',
                'tb_po.nomor_po'
            ])
            ->groupBy('tb_master_bahan.id', 'tb_master_bahan.bahan', 'tb_master_bahan.jenis', 'tb_po.tanggal_approve', 'tb_po.nomor_po');

        // Apply filters
        if (!empty($jenis)) {
            $query->where('tb_master_bahan.id', $jenis);
        }

        if (!empty($startDate) && !empty($endDate)) {
            try {
                $startDateFormatted = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
                $endDateFormatted = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
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
            return $item;
        });

        $title = 'Laporan Bahan Baku';
        $filterInfo = [];
        
        if (!empty($startDate) && !empty($endDate)) {
            $filterInfo[] = "Periode: {$startDate} - {$endDate}";
        }
        
        if (!empty($jenis)) {
            $filterInfo[] = "Jenis: " . ($jenisMap[$jenis] ?? 'Tidak Diketahui');
        }

        $pdf = PDF::loadView('office.laporanbahanbaku.pdf.export-pdf', compact('data', 'title', 'filterInfo'));
        $pdf->setPaper('A4', 'landscape');

        $filename = 'laporan-bahan-baku-' . date('Y-m-d-His') . '.pdf';
        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $jenis = $request->jenis;

        $filename = 'laporan-bahan-baku-' . date('Y-m-d-His') . '.xlsx';
        
        return Excel::download(new LaporanBahanBakuExport($startDate, $endDate, $jenis), $filename);
    }
}
