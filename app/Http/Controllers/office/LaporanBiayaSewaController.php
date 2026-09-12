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



        $query =  DB::table('tb_kas_kecil_transaksi')
            ->whereIn('master_bahan_id', [$id_infra->id])

            ->where('tb_kas_kecil_transaksi.status', 1)
            ->where('nomor_po', 'like', 'b.infra%')
            ->get();

        $total = DB::table('tb_kas_kecil_transaksi')
            ->whereIn('master_bahan_id', [$id_infra->id])
            ->where('tb_kas_kecil_transaksi.status', 1)
            //->whereNull('nomor_po')
            ->where('nomor_po', 'like', 'b.infra%')
            ->sum('jumlah');
        $total = $total;
        // filter tanggal jika ada
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $tanggalMulai = Carbon::createFromFormat('d-m-Y', $request->tanggal_mulai)->format('Y-m-d') . ' 00:00:00';
            $tanggalSelesai = Carbon::createFromFormat('d-m-Y', $request->tanggal_selesai)->format('Y-m-d') . ' 23:59:59';



            $query =  DB::table('tb_kas_kecil_transaksi')
                ->whereIn('master_bahan_id', [$id_infra->id])
                ->where('tb_kas_kecil_transaksi.status', 1)
                //->whereNull('nomor_po')
                ->where('nomor_po', 'like', 'b.infra%')
                ->whereBetween('tanggal', [
                    $tanggalMulai,
                    $tanggalSelesai
                ])
                ->get();

            $total = DB::table('tb_kas_kecil_transaksi')
                ->whereIn('master_bahan_id', [$id_infra->id])
                ->where('tb_kas_kecil_transaksi.status', 1)
                //->whereNull('nomor_po')
                ->where('nomor_po', 'like', 'b.infra%')
                ->whereBetween('tanggal', [
                    $tanggalMulai,
                    $tanggalSelesai
                ])
                ->sum('jumlah');
            $total = $total;
        }

        $data = $query;
        // PERBAIKAN: Menggunakan 'jumlah' sesuai nama kolom di database Anda


        return view('office.laporanakuntan.lbs', compact('data', 'total',  'header'));
    }

    public function cetak(Request $request)
    {
        $id_gaji = TbMasterBahan::where('bahan', 'Gaji Karyawan')->first();
        $id_infra = TbMasterBahan::where('bahan', 'Bantuan Peralatan Dan Infra')->first();

        $tanggalMulai = Carbon::createFromFormat('d-m-Y', $request->tanggal_mulai)->format('Y-m-d') . ' 00:00:00';
        $tanggalSelesai = Carbon::createFromFormat('d-m-Y', $request->tanggal_selesai)->format('Y-m-d') . ' 23:59:59';

        $start  = $tanggalMulai;
        $end    = $tanggalSelesai;

        $query =  DB::table('tb_kas_kecil_transaksi')
            ->whereIn('master_bahan_id', [$id_infra->id])
            ->where('tb_kas_kecil_transaksi.status', 1)
            //->whereNull('nomor_po')
            ->where('nomor_po', 'like', 'b.infra%')
            ->get();
        $total = DB::table('tb_kas_kecil_transaksi')
            ->whereIn('master_bahan_id', [$id_infra->id])
            ->where('tb_kas_kecil_transaksi.status', 1)
            //->whereNull('nomor_po')
            ->where('nomor_po', 'like', 'b.infra%')
            ->sum('jumlah');
        $total = $total;
        if ($start && $end) {
            $query =  DB::table('tb_kas_kecil_transaksi')
                ->whereIn('master_bahan_id', [$id_infra->id])
                ->where('tb_kas_kecil_transaksi.status', 1)
                //->whereNull('nomor_po')
                ->where('nomor_po', 'like', 'b.infra%')
                ->whereBetween('tanggal', [
                    $tanggalMulai,
                    $tanggalSelesai
                ])
                ->get();

            $total = DB::table('tb_kas_kecil_transaksi')
                ->whereIn('master_bahan_id', [$id_infra->id])
                ->where('tb_kas_kecil_transaksi.status', 1)
                //->whereNull('nomor_po')
                ->where('nomor_po', 'like', 'b.infra%')
                ->whereBetween('tanggal', [
                    $tanggalMulai,
                    $tanggalSelesai
                ])
                ->sum('jumlah');
            $total = $total;
        }


        $data = $query;

        // Bagian ini di fungsi cetak sudah benar dari awal


        $pdf = Pdf::loadView('office.laporanakuntan.lbspdf', compact('data',  'start', 'end', 'total'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('laporan_sewa' . date('Y-m-d_H-i-s') . '.pdf');

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
            ->where('master_bahan_id', 171)
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
