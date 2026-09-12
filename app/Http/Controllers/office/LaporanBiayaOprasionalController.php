<?php

namespace App\Http\Controllers\office;

use App\Exports\LaporanBiayaNonPanganExport;
use App\Http\Controllers\Controller;
use App\Models\KasKecilTransaksi;
use Illuminate\Http\Request;
use App\Models\LaporanBiayaOprasional;
use App\Models\TbMasterBahan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class LaporanBiayaOprasionalController extends Controller
{
    public function index(Request $request)
    {
        $header = "Laporan Biaya Operasional";

        $id_gaji = TbMasterBahan::where('bahan', 'Gaji Karyawan')->first();
        $id_infra = TbMasterBahan::where('bahan', 'Bantuan Peralatan Dan Infra')->first();


        $query1 = DB::table('tb_kas_kecil_transaksi as tkk')
            ->join('tb_po_close as pc', 'tkk.nomor_po', '=', 'pc.nomor_po') // pakai INNER JOIN
            ->where('tkk.status', 1)
            ->select(
                'tkk.jumlah',
                'tkk.deskripsi',
                'tkk.status',
                'pc.nomor_po',
                'pc.tanggal_tutup_po AS tanggal',
                'pc.jenis_po'
            )
            ->where('pc.jenis_po', 'operasional')
            ->get();
        $query =  DB::table('tb_kas_kecil_transaksi')
            ->whereNotIn('master_bahan_id', [$id_infra->id])
            ->where('tb_kas_kecil_transaksi.status', 1)
            ->whereNull('nomor_po')
            ->get();
        $total1 = DB::table('tb_kas_kecil_transaksi as tkk')
            ->join('tb_po_close as pc', 'tkk.nomor_po', '=', 'pc.nomor_po') // pakai INNER JOIN
            ->where('tkk.status', 1)
            ->select(
                'tkk.jumlah',
                'tkk.deskripsi',
                'tkk.status',
                'pc.nomor_po',
                'pc.tanggal_tutup_po AS tanggal',
                'pc.jenis_po'
            )
            ->where('pc.jenis_po', 'operasional')
            ->sum('tkk.jumlah');
        $total = DB::table('tb_kas_kecil_transaksi')
            ->where('tb_kas_kecil_transaksi.status', 1)
            ->whereNotIn('master_bahan_id', [$id_infra->id])
            ->whereNull('nomor_po')
            ->sum('jumlah');
        $total = $total + $total1;
        // filter tanggal jika ada
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $tanggalMulai = Carbon::createFromFormat('d-m-Y', $request->tanggal_mulai)->format('Y-m-d') . ' 00:00:00';
            $tanggalSelesai = Carbon::createFromFormat('d-m-Y', $request->tanggal_selesai)->format('Y-m-d') . ' 23:59:59';


            $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai]);
            $query1 = DB::table('tb_kas_kecil_transaksi as tkk')
                ->join('tb_po_close as pc', 'tkk.nomor_po', '=', 'pc.nomor_po') // pakai INNER JOIN
                ->where('tkk.status', 1)
                ->whereBetween('pc.tanggal_tutup_po', [
                    $tanggalMulai,
                    $tanggalSelesai
                ])
                ->select(
                    'tkk.jumlah',
                    'tkk.deskripsi',
                    'tkk.status',
                    'pc.nomor_po',
                    'pc.tanggal_tutup_po AS tanggal',
                    'pc.jenis_po'
                )
                ->where('pc.jenis_po', 'operasional')
                ->get();
            $query =  DB::table('tb_kas_kecil_transaksi')
                ->whereNotIn('master_bahan_id', [$id_infra->id])
                ->where('tb_kas_kecil_transaksi.status', 1)
                ->whereNull('nomor_po')
                ->whereBetween('tanggal', [
                    $tanggalMulai,
                    $tanggalSelesai
                ])
                ->get();
            $total1 = DB::table('tb_kas_kecil_transaksi as tkk')
                ->join('tb_po_close as pc', 'tkk.nomor_po', '=', 'pc.nomor_po') // pakai INNER JOIN
                ->where('tkk.status', 1)
                ->whereBetween('pc.tanggal_tutup_po', [
                    $tanggalMulai,
                    $tanggalSelesai
                ])
                ->select(
                    'tkk.jumlah',
                    'tkk.deskripsi',
                    'tkk.status',
                    'pc.nomor_po',
                    'pc.tanggal_tutup_po AS tanggal',
                    'pc.jenis_po'
                )
                ->where('pc.jenis_po', 'operasional')
                ->sum('tkk.jumlah');
            $total = DB::table('tb_kas_kecil_transaksi')
                ->whereNotIn('master_bahan_id', [$id_infra->id])
                ->where('tb_kas_kecil_transaksi.status', 1)
                ->whereNull('nomor_po')
                ->whereBetween('tanggal', [
                    $tanggalMulai,
                    $tanggalSelesai
                ])
                ->sum('jumlah');
            $total = $total + $total1;
        }

        $data = $query;
        $data1 = $query1;
        // PERBAIKAN: Menggunakan 'jumlah' sesuai nama kolom di database Anda
        

        return view('office.laporanakuntan.lbo', compact('data', 'total' ,'data1','header'));
    }

    public function cetak(Request $request)
    {
        $query = LaporanBiayaOprasional::query();
        $id_gaji = TbMasterBahan::where('bahan', 'Gaji Karyawan')->first();
        $id_infra = TbMasterBahan::where('bahan', 'Bantuan Peralatan Dan Infra')->first();

        $tanggalMulai = Carbon::createFromFormat('d-m-Y', $request->tanggal_mulai)->format('Y-m-d') . ' 00:00:00';
        $tanggalSelesai = Carbon::createFromFormat('d-m-Y', $request->tanggal_selesai)->format('Y-m-d') . ' 23:59:59';

        $start  = $tanggalMulai;
        $end    = $tanggalSelesai;

        $query1 = DB::table('tb_kas_kecil_transaksi as tkk')
            ->join('tb_po_close as pc', 'tkk.nomor_po', '=', 'pc.nomor_po') // pakai INNER JOIN
            ->where('tkk.status', 1)
            ->select(
                'tkk.jumlah',
                'tkk.deskripsi',
                'tkk.status',
                'pc.nomor_po',
                'pc.tanggal_tutup_po AS tanggal',
                'pc.jenis_po'
            )
            ->where('pc.jenis_po', 'operasional')
            ->get();
        $query =  DB::table('tb_kas_kecil_transaksi')
            ->whereNotIn('master_bahan_id', [$id_infra->id])
            ->where('tb_kas_kecil_transaksi.status', 1)
            ->whereNull('nomor_po')
            ->get();
        $total1 = DB::table('tb_kas_kecil_transaksi as tkk')
            ->join('tb_po_close as pc', 'tkk.nomor_po', '=', 'pc.nomor_po') // pakai INNER JOIN
            ->where('tkk.status', 1)
            ->select(
                'tkk.jumlah',
                'tkk.deskripsi',
                'tkk.status',
                'pc.nomor_po',
                'pc.tanggal_tutup_po AS tanggal',
                'pc.jenis_po'
            )
            ->where('pc.jenis_po', 'operasional')
            ->sum('tkk.jumlah');
        $total = DB::table('tb_kas_kecil_transaksi')
            ->whereNotIn('master_bahan_id', [$id_infra->id])
            ->where('tb_kas_kecil_transaksi.status', 1)
            ->whereNull('nomor_po')
            ->sum('jumlah');
        $total = $total + $total1;
        if ($start && $end) {
            $query1 = DB::table('tb_kas_kecil_transaksi as tkk')
                ->join('tb_po_close as pc', 'tkk.nomor_po', '=', 'pc.nomor_po') // pakai INNER JOIN
                ->where('tkk.status', 1)
                ->whereBetween('pc.tanggal_tutup_po', [
                    $tanggalMulai,
                    $tanggalSelesai
                ])
                ->select(
                    'tkk.jumlah',
                    'tkk.deskripsi',
                    'tkk.status',
                    'pc.nomor_po',
                    'pc.tanggal_tutup_po AS tanggal',
                    'pc.jenis_po'
                )
                ->where('pc.jenis_po', 'operasional')
                ->get();
            $query =  DB::table('tb_kas_kecil_transaksi')
                ->whereNotIn('master_bahan_id', [$id_infra->id])
                ->where('tb_kas_kecil_transaksi.status', 1)
                ->whereNull('nomor_po')
                ->whereBetween('tanggal', [
                    $tanggalMulai,
                    $tanggalSelesai
                ])
                ->get();
            $total1 = DB::table('tb_kas_kecil_transaksi as tkk')
                ->join('tb_po_close as pc', 'tkk.nomor_po', '=', 'pc.nomor_po') // pakai INNER JOIN
                ->where('tkk.status', 1)
                ->whereBetween('pc.tanggal_tutup_po', [
                    $tanggalMulai,
                    $tanggalSelesai
                ])
                ->select(
                    'tkk.jumlah',
                    'tkk.deskripsi',
                    'tkk.status',
                    'pc.nomor_po',
                    'pc.tanggal_tutup_po AS tanggal',
                    'pc.jenis_po'
                )
                ->where('pc.jenis_po', 'operasional')
                ->sum('tkk.jumlah');
            $total = DB::table('tb_kas_kecil_transaksi')
                ->whereNotIn('master_bahan_id', [$id_infra->id])
                ->where('tb_kas_kecil_transaksi.status', 1)
                ->whereNull('nomor_po')
                ->whereBetween('tanggal', [
                    $tanggalMulai,
                    $tanggalSelesai
                ])
                ->sum('jumlah');
            $total = $total + $total1;
        }


        $data = $query;
        $data1 = $query1;

        // Bagian ini di fungsi cetak sudah benar dari awal
        

        $pdf = Pdf::loadView('office.laporanakuntan.lbopdf', compact('data', 'data1', 'start', 'end', 'total'))
                ->setPaper('a4', 'portrait');

        return $pdf->download('laporan_lbo_' . date('Y-m-d_H-i-s') . '.pdf');

    }

    public function Lap_biaya_non_pangan_export(Request $request)
    {
        $start = $request->tanggal_mulai;
        $end   = $request->tanggal_selesai;


        $start = Carbon::createFromFormat('d-m-Y', $request->tanggal_mulai)->format('Y-m-d');
        $end = Carbon::createFromFormat('d-m-Y', $request->tanggal_selesai)->format('Y-m-d');
        $tanggalMulai = Carbon::createFromFormat('d-m-Y', $request->tanggal_mulai)->format('Y-m-d') . ' 00:00:00';
        $tanggalSelesai = Carbon::createFromFormat('d-m-Y', $request->tanggal_selesai)->format('Y-m-d') . ' 23:59:59';
        // 
        $data =  DB::select("
            SELECT 
                ROW_NUMBER() OVER (ORDER BY po.tanggal_digunakan, b.bahan) AS no_urut,
                b.id,
                b.bahan,
                SUM(po.jumlah_bahan) AS total_jumlah_bahan,
                SUM(po.jumlah_po) AS total_jumlah_po,
                po.tanggal_digunakan,
                s.satuan
            FROM tb_po_bahan po
            JOIN tb_master_bahan b ON po.id_bahan = b.id
            JOIN tb_satuan s ON po.satuan = s.id
            JOIN tb_po tp ON po.id_po = tp.id
            WHERE po.tanggal_digunakan BETWEEN ? AND ?
              AND tp.status_po = 'close'
              AND po.id_rincian_bahan = 0
            GROUP BY b.bahan, po.tanggal_digunakan, s.satuan
            ORDER BY po.tanggal_digunakan, b.bahan
        ", [$start, $end]);
        $data2 =  KasKecilTransaksi::where('status', 1)
            ->whereNull('nomor_po')
            ->whereBetween('tanggal', [
                $tanggalMulai,
                $tanggalSelesai
            ])
            ->get();





        return Excel::download(
            new LaporanBiayaNonPanganExport($data, $data2,$start, $end),
            'laporan_Non_pangan' . date('d-m-Y') . '.xlsx'
        );
    }
}