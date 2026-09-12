<?php

namespace App\Http\Controllers\pdf;

use App\Http\Controllers\Controller;
use App\Models\DataDapur;
use App\Models\RumusPerhitunganSayur;
use App\Models\Resep;
use App\Models\MasterBahanSayur;
use App\Models\Menu;
use App\Models\rincian_sekolah;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RekapSayurController extends Controller
{
    public function cetakRekapSayur($id_menu)
    {
        // Ambil data sayur + relasi ke resep + bahan
        $dataSayur = RumusPerhitunganSayur::with(['resep', 'bahan'])
            ->where('id_menu', $id_menu)
            ->get();

        // Bagian TABEL UTAMA: Master Sayur
        $sayurMasters = $dataSayur->map(function ($item, $i) {
            return (object)[
                'no' => $i + 1,
                'nama_sayur' => $item->resep->nama_resep ?? '-',
                'berat_bahan_baku_per_box' => $item->sayur_porsi_a ?? 0,
                'satuan' => $item->bahan->satuan_bahan ?? 'kg',
                'jenis' => $item->bahan->jenis ?? '-',
                'hasil_matang_setelah_penyusutan_kg' => $item->kebutuhan_matang_a ?? 0,
                'persentase_penyusutan' => $item->penyusutan_sayur_a ?? 0,
            ];
        });

        // Bagian TABEL KEDUA: Penerimaan
        $resepPenerimaan = $dataSayur->map(function ($item, $i) {
            return (object)[
                'no_resep' => $i + 1,
                'sayur' => $item->resep->nama_resep ?? '-',
                'jumlah' => $item->kebutuhan_total_matang ?? 0,
                'satuan_resep' => $item->bahan->satuan_bahan ?? 'kg',
                'jml_box' => '', // Kosongkan sesuai permintaan
                'total_box' => '',
                'isi_box_kg' => '',
            ];
        });

        // Bagian TABEL BAWAH: Kalkulasi Isian
        $dataKalkulasi = [
            'jumlah_pax_buffer' => '', // dikosongkan sesuai permintaan
            'pemorsian_gram' => '',
            'isian_sayur_1_nama' => '',
            'isian_sayur_1_kg_mentah' => '',
            'isian_sayur_1_kg_matang' => '',
            'isian_sayur_1_persen' => '',
            'isian_sayur_1_jenis' => '',
            'isian_sayur_2_nama' => '',
            'isian_sayur_2_kg_mentah' => '',
            'isian_sayur_2_kg_matang' => '',
            'isian_sayur_2_persen' => '',
            'isian_sayur_2_jenis' => '',
            'total_kg_mentah' => '',
            'total_kg_matang' => '',
            'total_persen' => '',
            'kebutuhan_bahan_baku_kg' => '',
            'kebutuhan_bahan_baku_matang_kali_masak' => '',
            'kebutuhan_sitno_persen' => '',
            'disamakan_maksimal_selisih_text' => '',
            'disamakan_maksimal_selisih_value' => '',
        ];

        $pdf = Pdf::loadView('pdf.rekapsayur', compact(
            'sayurMasters',
            'resepPenerimaan',
            'dataKalkulasi'
        ));

        $pdf->setPaper('a4', 'landscape');
        return $pdf->download('rekap.sayur_' . date('Ymd_His') . '.pdf');
    }

    public function generateRekapSayurAllImages($id_menu)
    {
        // Data Hardcoded untuk Tabel Master Sayuran
        /*$sayurMasters = [
            (object)['no' => 3, 'nama_sayuran' => 'Labu Siam Parut', 'berat_bahan_baku_per_box' => 5.00, 'satuan' => 'kg', 'jenis' => 'Buah', 'hasil_matang_setelah_penyusutan_kg' => 4.5, 'persentase_penyusutan' => 10],
            (object)['no' => 4, 'nama_sayuran' => 'Sawi Putih', 'berat_bahan_baku_per_box' => 3.00, 'satuan' => 'kg', 'jenis' => 'Daun', 'hasil_matang_setelah_penyusutan_kg' => 2.4, 'persentase_penyusutan' => 20],
            (object)['no' => 5, 'nama_sayuran' => 'Wortel', 'berat_bahan_baku_per_box' => 5.00, 'satuan' => 'kg', 'jenis' => 'Umbi', 'hasil_matang_setelah_penyusutan_kg' => 4.75, 'persentase_penyusutan' => 5],
            (object)['no' => 6, 'nama_sayuran' => 'Buncis', 'berat_bahan_baku_per_box' => 5.00, 'satuan' => 'kg', 'jenis' => 'Batang', 'hasil_matang_setelah_penyusutan_kg' => 4.5, 'persentase_penyusutan' => 10],
            (object)['no' => 7, 'nama_sayuran' => 'Labu Siam Kotak', 'berat_bahan_baku_per_box' => 4.00, 'satuan' => 'kg', 'jenis' => 'Buah', 'hasil_matang_setelah_penyusutan_kg' => 3.8, 'persentase_penyusutan' => 5],
            (object)['no' => 8, 'nama_sayuran' => 'Kacang Panjang', 'berat_bahan_baku_per_box' => 5.00, 'satuan' => 'kg', 'jenis' => 'Polong', 'hasil_matang_setelah_penyusutan_kg' => 4.75, 'persentase_penyusutan' => 5],
            (object)['no' => 9, 'nama_sayuran' => 'Pare/Jagung Muda', 'berat_bahan_baku_per_box' => 5.00, 'satuan' => 'kg', 'jenis' => 'Buah', 'hasil_matang_setelah_penyusutan_kg' => 4.75, 'persentase_penyusutan' => 5],
            (object)['no' => 10, 'nama_sayuran' => 'Jagung Polii', 'berat_bahan_baku_per_box' => 5.00, 'satuan' => 'kg', 'jenis' => 'Buah', 'hasil_matang_setelah_penyusutan_kg' => 4.75, 'persentase_penyusutan' => 5],
            (object)['no' => 11, 'nama_sayuran' => 'Pakcoy', 'berat_bahan_baku_per_box' => 3.00, 'satuan' => 'kg', 'jenis' => 'Daun', 'hasil_matang_setelah_penyusutan_kg' => 2.4, 'persentase_penyusutan' => 20],
            (object)['no' => 12, 'nama_sayuran' => 'Kol / Kubis', 'berat_bahan_baku_per_box' => 3.00, 'satuan' => 'kg', 'jenis' => 'Daun', 'hasil_matang_setelah_penyusutan_kg' => 2.4, 'persentase_penyusutan' => 20],
            (object)['no' => 13, 'nama_sayuran' => 'Sawi Hijau', 'berat_bahan_baku_per_box' => 3.00, 'satuan' => 'kg', 'jenis' => 'Daun', 'hasil_matang_setelah_penyusutan_kg' => 2.4, 'persentase_penyusutan' => 20],
            (object)['no' => 14, 'nama_sayuran' => 'Bunga Kol', 'berat_bahan_baku_per_box' => 4.00, 'satuan' => 'kg', 'jenis' => 'Bunga', 'hasil_matang_setelah_penyusutan_kg' => 3.8, 'persentase_penyusutan' => 5],
        ];*/

        $dapur = DataDapur::first();

        $results = DB::table('tb_box_bahan_baku as b')
            ->join('tb_master_bahan as m', 'b.id_bahan', '=', 'm.id')
            ->where('m.jenis', 3)
            ->select(
                'm.bahan as nama_sayuran',
                DB::raw('FORMAT(b.isi_per_box, 0) as berat_bahan_baku_per_box'),
                DB::raw("'Gram' as satuan"),
                DB::raw('FORMAT(b.hasil_matang, 0) as hasil_matang_setelah_penyusutan_kg'),
                'b.penyusutan as persentase_penyusutan',
                DB::raw("CASE WHEN m.jenis = 3 THEN 'Sayur' ELSE 'Lainnya' END as jenis")
            )
            ->limit(10)
            ->get();

        // Tambahkan nomor urut manual (seperti 'no' => 1, 2, ...)
        $sayurMasters = $results->map(function ($item, $index) {
            return (object)[
                'no' => $index + 1,
                'nama_sayuran' => $item->nama_sayuran,
                'berat_bahan_baku_per_box' => (float)str_replace(',', '', $item->berat_bahan_baku_per_box),
                //'berat_bahan_baku_per_box' => number_format($item->berat_bahan_baku_per_box,0,'.','.'),
                'satuan' => $item->satuan,
                'jenis' => 'sayur',
                'hasil_matang_setelah_penyusutan_kg' => (float)str_replace(',', '', $item->hasil_matang_setelah_penyusutan_kg),
                //'hasil_matang_setelah_penyusutan_kg' => (float) number_format($item->hasil_matang_setelah_penyusutan_kg,0,',','.'),
                'persentase_penyusutan' => (int)$item->persentase_penyusutan,
            ];
        });
        
        // Data Resep & Penerimaan
        /*$resepPenerimaan = [
            (object)['no_resep' => 18, 'sayuran' => 'Labu Siam Kotak', 'jumlah' => 2.00, 'satuan_resep' => 'kg', 
            'jml_box' => 1, 'total' => 5, 'isi_box_kg' => 4.00, 'persentase_isi_box' => 20],
            
            (object)['no_resep' => 19, 'sayuran' => 'Wortel', 'jumlah' => 5.00, 'satuan_resep' => 'kg', 'jml_box' => 1, 'total' => 1, 'isi_box_kg' => 5.00, 'persentase_isi_box' => 5],
            (object)['no_resep' => 20, 'sayuran' => '', 'jumlah' => 0.00, 'satuan_resep' => 'kg', 'jml_box' => null, 'total' => null, 'isi_box_kg' => null, 'persentase_isi_box' => '#VALUE!'],
            (object)['no_resep' => 21, 'sayuran' => '', 'jumlah' => 0.00, 'satuan_resep' => 'kg', 'jml_box' => null, 'total' => null, 'isi_box_kg' => null, 'persentase_isi_box' => '#VALUE!'],
        ];*/
       

        $menu           = Menu::find($id_menu);
        $jumlah_porsi   = rincian_sekolah::where('id_menu_harian', $id_menu)->sum('jumlah_penerima_total');

        $rumus_sayur    = RumusPerhitunganSayur::where('id_menu', $id_menu)->first(); 
        $sayur_1        = $results = DB::table('tb_menu_bahan as mn')
                        ->join('tb_master_bahan as mb', 'mn.bahan_id', '=', 'mb.id')
                        ->select('mn.menu_id','mb.bahan', 'mn.bahan_id', 'mn.status_bahan_baku')
                        ->where('mn.menu_id', $menu->sayur)
                        ->where('mn.status_bahan_baku', 1)
                        ->first();
        $sayur_2        = $results = DB::table('tb_menu_bahan as mn')
                        ->join('tb_master_bahan as mb', 'mn.bahan_id', '=', 'mb.id')
                        ->select('mn.menu_id','mb.bahan', 'mn.bahan_id', 'mn.status_bahan_baku')
                        ->where('mn.menu_id', $menu->sayur)
                        ->where('mn.status_bahan_baku', 2)
                        ->first();
        $sayur_3        = $results = DB::table('tb_menu_bahan as mn')
                        ->join('tb_master_bahan as mb', 'mn.bahan_id', '=', 'mb.id')
                        ->select('mn.menu_id','mb.bahan', 'mn.bahan_id', 'mn.status_bahan_baku')
                        ->where('mn.menu_id', $menu->sayur)
                        ->where('mn.status_bahan_baku', 4)
                        ->first();
        $sayur_4        = $results = DB::table('tb_menu_bahan as mn')
                        ->join('tb_master_bahan as mb', 'mn.bahan_id', '=', 'mb.id')
                        ->select('mn.menu_id', 'mn.bahan_id','mb.bahan', 'mn.status_bahan_baku')
                        ->where('mn.menu_id', $menu->sayur)
                        ->where('mn.status_bahan_baku', 5)
                        ->first();
        $data_bahan_harian = DB::table('rincian_menu_harian as rmh')
            ->join('tb_resep as r', 'rmh.id_resep', '=', 'r.id')
            ->join('tb_master_bahan as b', 'rmh.id_bahan', '=', 'b.id')
            ->join('tb_satuan as s', 'rmh.id_satuan', '=', 's.id')
            ->where('rmh.id_menu_harian', $id_menu)
            //->whereIn('rmh.id_resep', [$sayur_1->menu_id, ($sayur_2->menu_id ?? 0), ($sayur_3->menu_id??0), ($sayur_4->menu_id ?? 0)])
            ->whereIn('rmh.id_bahan', [$sayur_1->bahan_id, ($sayur_2->bahan_id ?? 0), ($sayur_3->bahan_id ?? 0), ($sayur_4->bahan_id ?? 0)])
            ->where('rmh.id_resep', $menu->sayur)
            ->select(
                'r.nama_resep as resep',
                'b.bahan',
                'rmh.jumlah',
                'rmh.jumlah_box',
                's.satuan'
            )
            ->get();

        $resepPenerimaan = $data_bahan_harian->map(function ($item, $index) {
            return (object)[
                'no' => $index + 1,
                'sayuran' => ($item->bahan??'-'),
                'jumlah' => (float)str_replace(',', '', ($item->jumlah??0)),
                //'berat_bahan_baku_per_box' => number_format($item->berat_bahan_baku_per_box,0,'.','.'),
                'satuan_resep' => ($item->satuan??'-'),
                'jml_box' => ($item->jumlah_box??'-'),
                'total' => (float)str_replace(',', '', ($item->jumlah_box??0)),
                //'hasil_matang_setelah_penyusutan_kg' => (float) number_format($item->hasil_matang_setelah_penyusutan_kg,0,',','.'),
                'isi_box_kg' => (float)(($item->jumlah??0) / ($item->jumlah_box??1)),
                'persentase_isi_box' => 0,
            ];
        });

        // Data Kalkulasi
        $dataKalkulasi = [
            'jumlah_pax_buffer' => $jumlah_porsi,
            'label_jumlah_pax' => 'Jumlah Pax',
            'pemorsian_gram_a' => $rumus_sayur->sayur_porsi_a,
            'pemorsian_gram_b' => $rumus_sayur->sayur_porsi_b,
            'isian_sayur_1_nama' => $sayur_1->bahan,
            'isian_sayur_1_kg_mentah' => $rumus_sayur->kebutuhan_sayur_a,
            'isian_sayur_1_matang' => $rumus_sayur->kebutuhan_matang_a,
            'isian_sayur_1_persen' => $rumus_sayur->penyusutan_sayur_a,
            'isian_sayur_1_jenis' => 'sayur',
            
            'isian_sayur_2_nama' => $sayur_2->bahan ?? '-',
            'isian_sayur_2_kg_mentah' => $rumus_sayur->kebutuhan_sayur_b ?? '-',
            'isian_sayur_2_kg_matang' => $rumus_sayur->kebutuhan_matang_b ?? '-',
            'isian_sayur_2_persen' => $rumus_sayur->kebutuhan_matang_b ?? '-',
            'isian_sayur_2_jenis' => 'sayur',

            'isian_sayur_3_nama' => $sayur_3->bahan ?? '-',
            'isian_sayur_3_kg_mentah' => $rumus_sayur->kebutuhan_sayur_c ?? '-',
            'isian_sayur_3_kg_matang' => $rumus_sayur->kebutuhan_matang_c ?? '-',
            'isian_sayur_3_persen' => $rumus_sayur->kebutuhan_matang_c ?? '-',
            'isian_sayur_3_jenis' => 'sayur',

            'isian_sayur_4_nama' => $sayur_4->bahan ?? '-',
            'isian_sayur_4_kg_mentah' => $rumus_sayur->kebutuhan_sayur_d ?? '-',
            'isian_sayur_4_kg_matang' => $rumus_sayur->kebutuhan_matang_d ?? '-',
            'isian_sayur_4_persen' => $rumus_sayur->kebutuhan_matang_d ?? '-',
            'isian_sayur_4_jenis' => 'sayur',

            'isian_sayur_total_kg_mentah' => $rumus_sayur->kebutuhan_total_mentah,
            'isian_sayur_total_kg_matang' => $rumus_sayur->kebutuhan_total_matang,
            'isian_sayur_total_persen' => 0,
            'kebutuhan_bahan_baku_kg' => $rumus_sayur->kebutuhan_total_mentah,
            'kebutuhan_bahan_baku_matang_kali_masak' => $rumus_sayur->kebutuhan_total_matang,
            'Jumlah_masak' => $rumus_sayur->jumlah_masak,
            'disamakan_maksimal_selisih_text' => 'disamakan maksimal selisih -0.25% sd 0',
            'disamakan_maksimal_selisih_value' => 25.0,
        ];

        // Tambahkan informasi pembuat dan penyetuju
        $dibuat_oleh = 'Asisten Dapur';
        $disetujui_oleh = 'Kepala Dapur';

        // Generate PDF
        $pdf = Pdf::loadView('pdf.rekapsayur', [
            'sayurMasters'      => $sayurMasters,
            'resepPenerimaan'   => $resepPenerimaan,
            'dataKalkulasi'     => $dataKalkulasi,
            'dibuat_oleh'       => $dibuat_oleh,
            'disetujui_oleh'    => $disetujui_oleh,
            'dapur'             => $dapur
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('rekap.sayur_' . date('Ymd_His') . '.pdf');
    }
}
