<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\LaporanHarianDapurExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\LaporanRealisasiAnggaran;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanHarianDapurExportController extends Controller
{
    /**
     * Download an exact copy of the example Excel template.
     * This returns dokumen/contoh.xlsx byte-for-byte so the
     * downloaded file is identical to the template.
     */
    public function download(Request $request)
    {
        // Use FromView export to render Blade view as Excel
        $data = [];
        $rows = request()->get('rows', 30);
        
        // Ambil tanggal dari request
        $periode_awal = $request->input('periode_awal');
        $periode_akhir = $request->input('periode_akhir');
        
        // Jika ada periode_awal dan periode_akhir dari request, cari berdasarkan itu
        // Jika tidak, ambil semua data laporan realisasi anggaran
        if ($periode_awal && $periode_akhir) {
            $data_list = LaporanRealisasiAnggaran::orderBy('periode_awal', 'asc')->get();
            
          
        } else {
            // Ambil semua data
            $data_list = LaporanRealisasiAnggaran::orderBy('periode_awal', 'asc')->get();
        }
        
        // Prepare data untuk setiap periode
        $all_data = [];
        foreach ($data_list as $data_anggaran) {
            $start = Carbon::parse($data_anggaran->periode_awal);
            $end   = Carbon::parse($data_anggaran->periode_akhir);

            // Loop tanggal dari awal hingga akhir
            $tanggalList = [];
            $pm = [];
            $menu_status = [];
            $po_status = [];
            $penerimaan_status = [];
            $masuk_gudang_status = [];
            $keluar_gudang_status = [];
            $hasil_masak_status = [];
            $hasil_scan_status = [];
            $surat_jalan_status = [];

            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $tanggalList[] = $date->format('Y-m-d');
                $pm[] = DB::table('rincian_sekolah')
                        ->join('tb_menu', 'rincian_sekolah.id_menu_harian', '=', 'tb_menu.id')
                        ->whereDate('tb_menu.tanggal_kirim', $date->format('Y-m-d'))
                        ->selectRaw('
                        SUM(rincian_sekolah.jumlah_penerima_total) as total_penerima,
                        SUM(rincian_sekolah.jumlah_penerima_a) as total_a,
                        SUM(rincian_sekolah.jumlah_penerima_b) as total_b
                    ')
                    ->first();
                $cek_menu = DB::table('tb_menu')
                            ->whereDate('tanggal_kirim', $date->format('Y-m-d'))
                            ->first();
                if (!$cek_menu) {
                    $menu_status[] = '-'; // Lewati iterasi ini jika tidak ada menu
                    
                }else{
                    $menu_status[] = 'v';
                }  
                     
                $cek_po = DB::table('tb_po_bahan')
                            ->whereDate('tanggal_digunakan', $date->format('Y-m-d'))
                            ->first();
                if (!$cek_po) {
                    $cek_po = '-'; // Lewati iterasi ini jika tidak ada PO
                }else{
                    $cek_po = 'v';
                } 
                $po_status[] = $cek_po;
                
                $cek_penerimaan = DB::table('tb_penerimaan')
                            ->whereDate('created_at', $date->format('Y-m-d'))
                            ->first();
                if (!$cek_penerimaan) {
                    $cek_penerimaan = '-'; // Lewati iterasi ini jika tidak ada penerimaan
                }else{
                    $cek_penerimaan = 'v';
                }
                $penerimaan_status[] = $cek_penerimaan;

                $cek_masuk_gudang = DB::table('warehouse_transaksi')
                            ->whereDate('tanggal_masuk', $date->format('Y-m-d'))
                            ->first();
                if (!$cek_masuk_gudang) {
                    $cek_masuk_gudang = '-'; // Lewati iterasi ini jika tidak
                }else{
                    $cek_masuk_gudang = 'v';
                }
                $masuk_gudang_status[] = $cek_masuk_gudang;

                $cek_keluar_gudang = DB::table('warehouse_transaksi')
                            ->whereDate('tanggal_keluar', $date->format('Y-m-d'))
                            ->first();
                if (!$cek_keluar_gudang) {
                    $cek_keluar_gudang = '-'; // Lewati iterasi ini jika tidak
                }else{
                    $cek_keluar_gudang = 'v';
                }
                $keluar_gudang_status[] = $cek_keluar_gudang;

                $cek_hasil_masak = DB::table('tb_hasil_masak')
                            ->whereDate('waktu_matang', $date->format('Y-m-d'))
                            ->first();
                if (!$cek_hasil_masak) {
                    $cek_hasil_masak = '-'; // Lewati iterasi ini jika tidak
                }else{
                    $cek_hasil_masak = 'v';
                }
                $hasil_masak_status[] = $cek_hasil_masak;

                $cek_hasil_scan = DB::table('tb_ompreng_transaksi')
                            ->whereDate('tanggal_keluar', $date->format('Y-m-d'))
                            ->first();
                if (!$cek_hasil_scan) {
                    $cek_hasil_scan = '-'; // Lewati iterasi ini jika tidak
                }else{
                    $cek_hasil_scan = 'v';
                }
                $hasil_scan_status[] = $cek_hasil_scan;

                $cek_surat_jalan = DB::table('surat_jalan')
                            ->whereDate('published_at', $date->format('Y-m-d'))
                            ->first();
                if (!$cek_surat_jalan) {
                    $cek_surat_jalan = '-'; // Lewati iterasi ini jika tidak
                }else{
                    $cek_surat_jalan = 'v';
                }
                $surat_jalan_status[] = $cek_surat_jalan;

            }
            
            $all_data[] = [
                'data_anggaran' => $data_anggaran,
                'tanggalList' => $tanggalList,
                'pm' => $pm,
                'menu_status' => $menu_status,
                'po_status' => $po_status,
                'penerimaan_status' => $penerimaan_status,
                'masuk_gudang_status' => $masuk_gudang_status,
                'keluar_gudang_status' => $keluar_gudang_status,
                'hasil_masak_status' => $hasil_masak_status,
                'hasil_scan_status' => $hasil_scan_status,
                'surat_jalan_status' => $surat_jalan_status,
            ];
        }

        $export = new LaporanHarianDapurExport($all_data, $rows);
        $downloadName = 'laporan_harian_dapur.xlsx';

        return Excel::download($export, $downloadName);
    }
}
