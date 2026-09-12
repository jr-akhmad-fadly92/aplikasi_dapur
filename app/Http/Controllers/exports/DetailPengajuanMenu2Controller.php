<?php

namespace App\Http\Controllers\exports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailPengajuanMenu2Controller extends Controller
{
    public function index(Request $request)
    {
        // Ambil data sesuai kebutuhan, misal dari model
        // $data = Model::where(...)->get();
        // return view dengan data
        return view('exports.detail_pengajuan_menu_2');
    }

    public function exportExcel(Request $request, $menuId)
    {
        $rows_karbo_utama = DB::table('tb_menu as m')
            ->join('tb_resep as r', 'm.karbohidrat', '=', 'r.id')
            ->join('tb_menu_bahan as mmb', 'mmb.menu_id', '=', 'r.id')
            ->join('tb_master_bahan as mb', 'mmb.bahan_id', '=', 'mb.id')
            ->join('rincian_menu_harian as rmh', function ($join) {
                $join->on('rmh.id_menu_harian', '=', 'm.id')
                     ->on('rmh.id_bahan', '=', 'mb.id');
            })
            ->join('tb_satuan as s', 'rmh.id_satuan', '=', 's.id')
            ->leftJoin('tb_rumus_perhitungan_karbo as rpk', 'rpk.id_menu', '=', 'm.id')
            ->select(
                'm.id as menu_id',
                'r.nama_resep',
                'mb.bahan',
                'rmh.jumlah',
                'rmh.harga',
                's.satuan',
                'rpk.karbo_porsi_a',
                'rpk.karbo_porsi_b'
            )
            ->where('m.id', $menuId)
            ->where('mmb.status_bahan_baku', '<>', 3)
            ->get();
        $rows_karbo_bumbu = DB::table('tb_menu as m')
            ->join('tb_resep as r', 'm.karbohidrat', '=', 'r.id')
            ->join('tb_menu_bahan as mmb', 'mmb.menu_id', '=', 'r.id')
            ->join('tb_master_bahan as mb', 'mmb.bahan_id', '=', 'mb.id')
            ->join('rincian_menu_harian as rmh', function ($join) {
                $join->on('rmh.id_menu_harian', '=', 'm.id')
                     ->on('rmh.id_bahan', '=', 'mb.id');
            })
            ->join('tb_satuan as s', 'rmh.id_satuan', '=', 's.id')
            ->leftJoin('tb_rumus_perhitungan_karbo as rpk', 'rpk.id_menu', '=', 'm.id')
            ->select(
                'm.id as menu_id',
                'r.nama_resep',
                'mb.bahan',
                'rmh.harga',
                'rmh.jumlah',
                's.satuan',
                'rpk.karbo_porsi_a',
                'rpk.karbo_porsi_b'
            )
            ->where('m.id', $menuId)
            ->whereColumn('rmh.id_resep', 'm.karbohidrat')
            ->where('mmb.status_bahan_baku', '==', 3)
            ->get();

        $rows_protein_utama = DB::table('tb_menu as m')
            ->join('tb_resep as r', 'm.protein', '=', 'r.id')
            ->join('tb_menu_bahan as mmb', 'mmb.menu_id', '=', 'r.id')
            ->join('tb_master_bahan as mb', 'mmb.bahan_id', '=', 'mb.id')
            ->join('rincian_menu_harian as rmh', function ($join) {
                $join->on('rmh.id_menu_harian', '=', 'm.id')
                     ->on('rmh.id_bahan', '=', 'mb.id');
            })
            ->join('tb_satuan as s', 'rmh.id_satuan', '=', 's.id')
            ->leftJoin('tb_rumus_perhitungan_protein as rpk', 'rpk.id_menu', '=', 'm.id')
            ->select(
                'm.id as menu_id',
                'r.nama_resep',
                'mb.bahan',
                'rmh.jumlah',
                'rmh.harga',
                's.satuan',
                'rpk.protein_porsi_a',
                'rpk.protein_porsi_b'
            )
            ->where('m.id', $menuId)
            ->where('mmb.status_bahan_baku', '<>', 3)
            ->get();

        $rows_protein_bumbu = DB::table('tb_menu as m')
            ->join('tb_resep as r', 'm.protein', '=', 'r.id')
            ->join('tb_menu_bahan as mmb', 'mmb.menu_id', '=', 'r.id')
            ->join('tb_master_bahan as mb', 'mmb.bahan_id', '=', 'mb.id')
            ->join('rincian_menu_harian as rmh', function ($join) {
                $join->on('rmh.id_menu_harian', '=', 'm.id')
                     ->on('rmh.id_bahan', '=', 'mb.id');
            })
            ->join('tb_satuan as s', 'rmh.id_satuan', '=', 's.id')
            ->leftJoin('tb_rumus_perhitungan_protein as rpk', 'rpk.id_menu', '=', 'm.id')
            ->select(
                'm.id as menu_id',
                'r.nama_resep',
                'mb.bahan',
                'rmh.jumlah',
                'rmh.harga',
                's.satuan',
                'rpk.protein_porsi_a',
                'rpk.protein_porsi_b'
            )
            ->where('m.id', $menuId)
            ->whereColumn('rmh.id_resep', 'm.protein')
            ->where('mmb.status_bahan_baku', '=', 3)
            ->get();
        
        $rows_sayur_utama = DB::table('tb_menu as m')
            ->join('tb_resep as r', 'm.sayur', '=', 'r.id')
            ->join('tb_menu_bahan as mmb', 'mmb.menu_id', '=', 'r.id')
            ->join('tb_master_bahan as mb', 'mmb.bahan_id', '=', 'mb.id')
            ->join('rincian_menu_harian as rmh', function ($join) {
                $join->on('rmh.id_menu_harian', '=', 'm.id')
                     ->on('rmh.id_bahan', '=', 'mb.id');
            })
            ->join('tb_satuan as s', 'rmh.id_satuan', '=', 's.id')
            ->leftJoin('tb_rumus_perhitungan_sayur as rpk', 'rpk.id_menu', '=', 'm.id')
            ->select(
                'm.id as menu_id',
                'r.nama_resep',
                'mb.bahan',
                'rmh.jumlah',
                's.satuan',
                'rpk.sayur_porsi_a',
                'rpk.sayur_porsi_b'
            )
            ->where('m.id', $menuId)
            ->where('mmb.status_bahan_baku', '<>', 3)
            ->get();
        $rows_sayur_bumbu = DB::table('tb_menu as m')
            ->join('tb_resep as r', 'm.sayur', '=', 'r.id')
            ->join('tb_menu_bahan as mmb', 'mmb.menu_id', '=',  'r.id')
            ->join('tb_master_bahan as mb', 'mmb.bahan_id', '=', 'mb.id')
            ->join('rincian_menu_harian as rmh', function ($join) { 
                $join->on('rmh.id_menu_harian', '=', 'm.id')
                     ->on('rmh.id_bahan', '=', 'mb.id');
            })
            ->join('tb_satuan as s', 'rmh.id_satuan', '=', 's.id')
            ->leftJoin('tb_rumus_perhitungan_sayur as rpk', 'rpk.id_menu', '=', 'm.id')
            ->select(
                'm.id as menu_id',
                'r.nama_resep',
                'mb.bahan',
                'rmh.jumlah',
                'rmh.harga',
                's.satuan',
                'rpk.sayur_porsi_a',
                'rpk.sayur_porsi_b'
            )   
            ->where('m.id', $menuId)
            ->whereColumn('rmh.id_resep', 'm.sayur')
            ->where('mmb.status_bahan_baku', '=', 3)
            ->get();



        $rows_buah_utama = DB::table('tb_menu as m')
            ->join('tb_resep as r', 'm.buah', '=', 'r.id')
            ->join('tb_menu_bahan as mmb', 'mmb.menu_id', '=', 'r.id')
            ->join('tb_master_bahan as mb', 'mmb.bahan_id', '=', 'mb.id')
            ->join('rincian_menu_harian as rmh', function ($join) {
                $join->on('rmh.id_menu_harian', '=', 'm.id')
                     ->on('rmh.id_bahan', '=', 'mb.id');
            })
            ->join('tb_satuan as s', 'rmh.id_satuan', '=', 's.id')
            ->leftJoin('tb_rumus_perhitungan_buah as rpk', 'rpk.id_menu', '=', 'm.id')
            ->select(
                'm.id as menu_id',
                'r.nama_resep',
                'mb.bahan',
                'rmh.jumlah',
                'rmh.harga',
                's.satuan',
                'rpk.buah_porsi_a',
                'rpk.buah_porsi_b'
            )
            ->where('m.id', $menuId)
            ->where('mmb.status_bahan_baku', '<>', 3)
            ->get();
        $rows_buah_bumbu = DB::table('tb_menu as m')
            ->join('tb_resep as r', 'm.buah', '=', 'r.id')
            ->join('tb_menu_bahan as mmb', 'mmb.menu_id', '=',  'r.id')
            ->join('tb_master_bahan as mb', 'mmb.bahan_id', '=', 'mb.id')
            ->join('rincian_menu_harian as rmh', function ($join) { 
                $join->on('rmh.id_menu_harian', '=', 'm.id')
                     ->on('rmh.id_bahan', '=', 'mb.id');
            })
            ->join('tb_satuan as s', 'rmh.id_satuan', '=', 's.id')
            ->leftJoin('tb_rumus_perhitungan_buah as rpk', 'rpk.id_menu', '=', 'm.id')
            ->select(
                'm.id as menu_id',
                'r.nama_resep',
                'mb.bahan',
                'rmh.jumlah',
                'rmh.harga',
                's.satuan',
                'rpk.buah_porsi_a',
                'rpk.buah_porsi_b'
            )   
            ->where('m.id', $menuId)
            ->whereColumn('rmh.id_resep', 'm.buah')
            ->where('mmb.status_bahan_baku', '=', 3)
            ->get();
        
        $rows_pendamping_utama = DB::table('tb_menu as m')
            ->join('tb_resep as r', 'm.susu', '=', 'r.id')
            ->join('tb_menu_bahan as mmb', 'mmb.menu_id', '=', 'r.id')
            ->join('tb_master_bahan as mb', 'mmb.bahan_id', '=', 'mb.id')
            ->join('rincian_menu_harian as rmh', function ($join) {
                $join->on('rmh.id_menu_harian', '=', 'm.id')
                     ->on('rmh.id_bahan', '=', 'mb.id');
            })
            ->join('tb_satuan as s', 'rmh.id_satuan', '=', 's.id')
            ->leftJoin('tb_rumus_perhitungan_suplemen as rpk', 'rpk.id_menu', '=', 'm.id')
            ->select(
                'm.id as menu_id',
                'r.nama_resep',
                'mb.bahan',
                'rmh.jumlah',
                'rmh.harga',
                's.satuan',
                'rpk.suplemen_porsi_a',
                'rpk.suplemen_porsi_b'
            )
            ->where('m.id', $menuId)
            ->where('mmb.status_bahan_baku', '<>', 3)
            ->get();
        $rows_pendamping_bumbu = DB::table('tb_menu as m')
            ->join('tb_resep as r', 'm.susu', '=', 'r.id')
            ->join('tb_menu_bahan as mmb', 'mmb.menu_id', '=',  'r.id')
            ->join('tb_master_bahan as mb', 'mmb.bahan_id', '=', 'mb.id')
            ->join('rincian_menu_harian as rmh', function ($join) { 
                $join->on('rmh.id_menu_harian', '=', 'm.id')
                     ->on('rmh.id_bahan', '=', 'mb.id');
            })
            ->join('tb_satuan as s', 'rmh.id_satuan', '=', 's.id')
            ->leftJoin('tb_rumus_perhitungan_suplemen as rpk', 'rpk.id_menu', '=', 'm.id')
            ->select(
                'm.id as menu_id',
                'r.nama_resep',
                'mb.bahan',
                'rmh.jumlah',
                'rmh.harga',
                's.satuan',
                'rpk.suplemen_porsi_a',
                'rpk.suplemen_porsi_b'
            )   
            ->where('m.id', $menuId)
            ->whereColumn('rmh.id_resep', 'm.susu')
            ->where('mmb.status_bahan_baku', '=', 3)
            ->get();

        $rows_rekap_bumbu =  DB::table('tb_menu as m')

            ->join('rincian_menu_harian as rmh', function ($join) {
                $join->on('rmh.id_menu_harian', '=', 'm.id');
            })

            ->join('tb_menu_bahan as mmb', function ($join) {
                $join->on('mmb.menu_id', '=', 'rmh.id_resep');
            })

            ->join('tb_master_bahan as mb', function ($join) {
                $join->on('mmb.bahan_id', '=', 'mb.id')
                     ->on('rmh.id_bahan', '=', 'mb.id');
            })

            ->join('tb_satuan as s', 'rmh.id_satuan', '=', 's.id')

            ->leftJoin('tb_rumus_perhitungan_suplemen as rpk',
                'rpk.id_menu', '=', 'm.id')

            ->select(
                'm.id as menu_id',
                'mb.bahan',
                DB::raw('SUM(rmh.jumlah) as total_jumlah'),
                DB::raw('MIN(rmh.harga) as harga'), // ambil harga pertama
                's.satuan',
                'rpk.suplemen_porsi_a',
                'rpk.suplemen_porsi_b'
            )

            ->where('m.id', $menuId)
            ->where('mmb.status_bahan_baku', 3)

            ->groupBy(
                'm.id',
                'mb.id',
                'mb.bahan',
                's.satuan',
                'rpk.suplemen_porsi_a',
                'rpk.suplemen_porsi_b'
            )

            ->get();

            $data = [
            'rows_karbo_utama' => $rows_karbo_utama,
            'rows_karbo_bumbu' => $rows_karbo_bumbu,
            'rows_protein_utama' => $rows_protein_utama,
            'rows_protein_bumbu' => $rows_protein_bumbu,
            'rows_sayur_utama' => $rows_sayur_utama,
            'rows_sayur_bumbu' => $rows_sayur_bumbu,
            'rows_buah_utama' => $rows_buah_utama,
            'rows_buah_bumbu' => $rows_buah_bumbu,
            'rows_pendamping_utama' => $rows_pendamping_utama,
            'rows_pendamping_bumbu' => $rows_pendamping_bumbu,  
            'rows_rekap_bumbu' => $rows_rekap_bumbu,
            'menuId' => $menuId,
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\DetailPengajuanMenu2Export($data),
            'detail_pengajuan_menu_2.xlsx'
        );
    }
}
