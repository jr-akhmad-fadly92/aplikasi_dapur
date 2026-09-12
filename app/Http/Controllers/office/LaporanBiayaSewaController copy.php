<?php

namespace App\Http\Controllers\office;

use App\Exports\LaporanBiayaNonPanganExport;
use App\Exports\LaporanBiayaSewaExport;
use App\Http\Controllers\Controller;
use App\Models\KasKecilTransaksi;
use Illuminate\Http\Request;
use App\Models\LaporanBiayaOprasional;
use App\Models\TbMasterBahan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class LaporanBiayaSewaController extends Controller
{
    public function index(Request $request)
    {
        $header = "Laporan Biaya Infrastruktur dan Peralatan";

        $id_gaji = TbMasterBahan::where('bahan', 'Gaji Karyawan')->first();
        $id_infra = TbMasterBahan::where('bahan', 'Bantuan Peralatan Dan Infra')->first();


       
        $query_baru =  DB::table('tb_kas_kecil_transaksi')
            ->where('master_bahan_id', '<>', 221)
        
            ->get();
        
        $total = DB::table('tb_kas_kecil_transaksi')
            ->whereIn('master_bahan_id', [$id_infra->id])
            ->whereNotNull('master_bahan_id')
           
            ->sum('jumlah');
        $total = $total ;
        // filter tanggal jika ada
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $tanggalMulai = Carbon::createFromFormat('d-m-Y', $request->tanggal_mulai)->format('Y-m-d');
            $tanggalSelesai = Carbon::createFromFormat('d-m-Y', $request->tanggal_selesai)->format('Y-m-d');


            $query_baru =  DB::table('tb_kas_kecil_transaksi')
                ->where('master_bahan_id', '<>', 221)

                ->get();
            
            $total = DB::table('tb_kas_kecil_transaksi')
                ->whereIn('master_bahan_id', [$id_infra->id])
                ->whereNull('nomor_po')
                ->whereNotNull('master_bahan_id')
                ->whereBetween('tanggal', [
                    $tanggalMulai,
                    $tanggalSelesai
                ])
                ->sum('jumlah');
            $total = $total ;
        }
        dd($query_baru->toSql(), $query_baru->getBindings());
        $data = $query_baru;
        // PERBAIKAN: Menggunakan 'jumlah' sesuai nama kolom di database Anda


        return view('office.laporanakuntan.lbs', compact('data', 'total',  'header'));
     
    }

    public function cetak(Request $request)
    {
        $semuaDataLaporan = [
            ['tanggal' => '2025-07-01', 'uraian' => '-', 'nominal' => '548.000', 'keterangan' => 'Revisi'],
            ['tanggal' => '2025-07-02', 'uraian' => 'Sewa Ruangan', 'nominal' => '1.200.000', 'keterangan' => ''],
            ['tanggal' => '2025-07-03', 'uraian' => 'Transport', 'nominal' => '350.000', 'keterangan' => ''],
            ['tanggal' => '2025-08-15', 'uraian' => 'Biaya Promosi', 'nominal' => '750.000', 'keterangan' => 'Promosi online'],
            ['tanggal' => '2025-08-20', 'uraian' => 'Sewa Gudang', 'nominal' => '2.500.000', 'keterangan' => ''],
            ['tanggal' => '2024-12-10', 'uraian' => 'Biaya Listrik', 'nominal' => '400.000', 'keterangan' => ''],
        ];

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $laporan_terfilter = array_filter($semuaDataLaporan, function($item) use ($start_date, $end_date) {
            $item_tanggal = strtotime($item['tanggal']);
            if ($start_date && $end_date) {
                return ($item_tanggal >= strtotime($start_date) && $item_tanggal <= strtotime($end_date));
            }
            return true;
        });

        $total_nominal = array_sum(array_map(function($item) {
            return (int)str_replace('.', '', $item['nominal']);
        }, $laporan_terfilter));

        $formatted_total = number_format($total_nominal, 0, ',', '.');
        
        $kepala_sppg = $request->input('kepala_sppg');
        $akuntansi_sppg = $request->input('akuntansi_sppg');

        $data = [
            'laporan' => array_values($laporan_terfilter),
            'total' => $formatted_total,
            'kepala_sppg' => $kepala_sppg,
            'akuntansi_sppg' => $akuntansi_sppg,
            'tanggal_ttd' => date('d-m-Y')
        ];

        $pdf = Pdf::loadView('office.laporanakuntan.lbspdf', compact('data'));
        return $pdf->download('laporan_sewa.pdf');
    }

    public function Lap_biaya_Sewa_export(Request $request)
    {
        $start = $request->tanggal_mulai;
        $end   = $request->tanggal_selesai;


        $start = Carbon::createFromFormat('d-m-Y', $request->tanggal_mulai)->format('Y-m-d');
        $end = Carbon::createFromFormat('d-m-Y', $request->tanggal_selesai)->format('Y-m-d');
        $tanggalMulai = Carbon::createFromFormat('d-m-Y', $request->tanggal_mulai)->format('Y-m-d') . ' 00:00:00';
        $tanggalSelesai = Carbon::createFromFormat('d-m-Y', $request->tanggal_selesai)->format('Y-m-d') . ' 23:59:59';
        // 
       
        $data2 =  KasKecilTransaksi::where('status', 1)
            ->where('master_bahan_id',171)
            ->whereBetween('tanggal', [
                $tanggalMulai,
                $tanggalSelesai
            ])
            ->get();





        return Excel::download(
            new LaporanBiayaSewaExport($data2, $start, $end),
            'laporan_Biaya_Sewa' . date('d-m-Y') . '.xlsx'
        );
    }
}