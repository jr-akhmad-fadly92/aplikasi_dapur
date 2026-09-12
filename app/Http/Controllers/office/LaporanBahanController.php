<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TbPo;
use App\Models\TbPoBahan;
use App\Models\TbMasterBahan;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\LaporanBahanExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class LaporanBahanController extends Controller
{
    public function index()
    {
        $header = "Laporan PO Pembelian";

        return view('office.laporanbahan.index', compact('header'));
    }

 
public function getData(Request $request)
{
    // Build query (belum dipanggil ->get())
    $query = DB::table('tb_po_bahan')
        ->join('tb_po', 'tb_po_bahan.id_po', '=', 'tb_po.id')
        ->join('tb_master_bahan', 'tb_po_bahan.id_bahan', '=', 'tb_master_bahan.id')
        ->whereNotNull('tb_po.tanggal_approve');

    // Filter berdasarkan tanggal jika ada (lakukan sebelum agregasi)
    if ($request->filled('start_date') && $request->filled('end_date')) {
        try {
            $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
            $endDate = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
            $query->whereBetween('tb_po.tanggal_approve', [$startDate, $endDate]);
        } catch (\Exception $e) {
            // format salah -> abaikan filter
        }
    }

    // Apply select, groupBy, orderBy pada query builder
    $query = $query->select([
            'tb_po.nomor_po',
            DB::raw('SUM(tb_po_bahan.jumlah_bahan) AS total_jumlah_bahan'),
            DB::raw('MIN(tb_po_bahan.jumlah_po) AS jumlah_po'),
            DB::raw('MIN(tb_po.tanggal_approve) AS tanggal_approve'),
            DB::raw('MIN(tb_master_bahan.bahan) AS bahan'),
            // aman dari division by zero
            DB::raw('(MIN(tb_po_bahan.jumlah_po) / NULLIF(SUM(tb_po_bahan.jumlah_bahan), 0)) AS harga_satuan')
        ])
        ->groupBy('tb_po.nomor_po')
        // orderBy berdasarkan alias tanggal_approve (alias tersedia karena di SELECT)
        ->orderBy('tanggal_approve', 'asc')
        ->orderBy('tb_po.nomor_po', 'asc');

    // Mengirimkan ke DataTables (boleh berupa query builder)
    return DataTables::of($query)
        ->addIndexColumn()
        ->addColumn('tanggal', function ($row) {
            return $row->tanggal_approve ? Carbon::parse($row->tanggal_approve)->format('d/m/Y') : '-';
        })
        ->addColumn('nomor_po', function ($row) {
            return $row->nomor_po ?? '-';
        })
        ->addColumn('bahan', function ($row) {
            return $row->bahan ?? '-';
        })
        ->addColumn('jumlah', function ($row) {
            // gunakan alias total_jumlah_bahan
            $jumlah = isset($row->total_jumlah_bahan) ? number_format($row->total_jumlah_bahan, 0, ',', '.') : '0';
            return $jumlah;
        })
        ->addColumn('total_harga', function ($row) {
            $harga = $row->jumlah_po ?? 0;
            return 'Rp ' . number_format($harga, 0, ',', '.');
        })
        ->addColumn('harga_satuan', function ($row) {
            // jika null (division by zero) tampilkan '-'
            if (!isset($row->harga_satuan) || $row->harga_satuan === null) {
                return '-';
            }
            return 'Rp ' . number_format($row->harga_satuan, 0, ',', '.');
        })
        ->rawColumns(['tanggal', 'nomor_po', 'bahan', 'jumlah', 'total_harga', 'harga_satuan'])
        ->make(true);
}


    public function export(Request $request)
    {
        // Method untuk export Excel (akan dibuat nanti jika diperlukan)
        // TODO: Implementasi export ke Excel

        return response()->json([
            'message' => 'Export feature akan diimplementasikan'
        ]);
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        if (!$startDate || !$endDate) {
            return response()->json(['error' => 'Tanggal mulai dan tanggal berakhir harus diisi'], 400);
        }

        try {
            $startDateCarbon = Carbon::createFromFormat('d/m/Y', $startDate);
            $endDateCarbon = Carbon::createFromFormat('d/m/Y', $endDate);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Format tanggal tidak valid. Gunakan format DD/MM/YYYY'], 400);
        }

        try {
            ini_set('memory_limit', '512M');
            ini_set('max_execution_time', 300);

            // ---- QUERY FIX ----
            $query = DB::table('tb_po_bahan')
                ->join('tb_po', 'tb_po_bahan.id_po', '=', 'tb_po.id')
                ->join('tb_master_bahan', 'tb_po_bahan.id_bahan', '=', 'tb_master_bahan.id')
                ->whereNotNull('tb_po.tanggal_approve')
                ->whereBetween('tb_po.tanggal_approve', [
                    $startDateCarbon->format('Y-m-d'),
                    $endDateCarbon->format('Y-m-d')
                ])
                ->select([
                    'tb_po.nomor_po',
                    DB::raw('SUM(tb_po_bahan.jumlah_bahan) AS total_jumlah_bahan'),
                    DB::raw('MIN(tb_po_bahan.jumlah_po) AS jumlah_po'),
                    DB::raw('MIN(tb_po.tanggal_approve) AS tanggal_approve'),
                    DB::raw('MIN(tb_master_bahan.bahan) AS bahan')
                ])
                ->groupBy('tb_po.nomor_po')
                ->orderBy('tanggal_approve', 'asc');

            // Hitung total record
            $totalRecords = $query->count();
            $maxRecords = 5000;
            $isLimited = false;

            if ($totalRecords > $maxRecords) {
                $isLimited = true;
                $data = $query->limit($maxRecords)->get();
            } else {
                $data = $query->get();
            }

            // Total harga = jumlah_po (diambil min) bukan sum
            $totalHarga = $data->sum('jumlah_po');

            $pdfData = [
                'data' => $data,
                'totalHarga' => $totalHarga,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'isLimited' => $isLimited,
                'totalRecords' => $totalRecords,
                'maxRecords' => $maxRecords
            ];

            $pdf = Pdf::loadView('office.laporanbahan.pdf.pdf', $pdfData)
                ->setPaper('A4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'defaultFont' => 'Arial'
                ]);

            $fileName = 
                'Laporan_PO_Pembelian_' . 
                str_replace('/', '-', $startDate) . 
                '_sampai_' . 
                str_replace('/', '-', $endDate) . '.pdf';

            return $pdf->download($fileName);

        } catch (\Exception $e) {
            Log::error('Export PDF Error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }


    public function exportExcel(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Validasi tanggal
        if (!$startDate || !$endDate) {
            return redirect()->back()->with('error', 'Tanggal mulai dan tanggal berakhir harus diisi');
        }

        try {
            // Validasi format tanggal
            $parsedStartDate = Carbon::createFromFormat('d/m/Y', $startDate);
            $parsedEndDate = Carbon::createFromFormat('d/m/Y', $endDate);
        } catch (\Exception $e) {
            $errorMessage = 'Format tanggal tidak valid. Gunakan format DD/MM/YYYY';
            return redirect()->back()->with('error', $errorMessage);
        }

        try {
            // Optimasi: Set memory limit yang wajar dan execution time
            ini_set('memory_limit', '256M');
            ini_set('max_execution_time', 120);

            // Generate nama file dengan tanggal
            $fileName = 'Laporan_PO_Pembelian_' . str_replace('/', '-', $startDate) . '_sampai_' . str_replace('/', '-', $endDate) . '.xlsx';

            // Langsung return Excel download tanpa pengecekan berlebihan
            return Excel::download(new LaporanBahanExport($startDate, $endDate), $fileName);

        } catch (\Exception $e) {
            Log::error('Export Excel Error', [
                'message' => $e->getMessage(),
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);

            $errorMessage = 'Terjadi kesalahan saat export: ' . $e->getMessage();
            return redirect()->back()->with('error', $errorMessage);
        }
    }
}
