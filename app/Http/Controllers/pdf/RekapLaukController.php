<?php

namespace App\Http\Controllers\pdf;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuBahan;
use App\Models\rincian_menu_harian;
use App\Models\rincian_sekolah;
use App\Models\RumusPerhitunganProtein;
use App\Models\RincianMenuProtein;
use App\Models\RumusResep;
use App\Models\TbRincianMenuTemp;
use App\Models\TbSatuan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;

class RekapLaukController extends Controller
{
    public function cetakRekapLauk($id_menu)
    {
        $protein = RumusPerhitunganProtein::with(['rincianMenuProtein.resep'])
            ->where('id_menu', $id_menu)
            ->first();

        if (!$protein) {
            return abort(404, 'Data tidak ditemukan');
        }

        /* ----------------------------------------------------------
           1. Ambil semua resep dengan id_komponen_sehat = 2
           2. Hilangkan duplikat berdasarkan resep.id
        ---------------------------------------------------------- */
        $rincianLauk = $protein->rincianMenuProtein
            ->filter(fn($r) => $r->resep && $r->resep->id_komponen_sehat == 2)
            ->unique('resep.id')   // agar nama tidak berulang
            ->values();            // reset indeks array

        /* ----------------------------------------------------------
           Tabel “Pilihan Nama Lauk” – tampilkan semua baris
        ---------------------------------------------------------- */
        $laukMasters = $rincianLauk->map(function ($rincian, $i) {
            $jumlah = $rincian->jumlah ?? 0;
            $hasil_matang = $jumlah * 0.9;
            $persen_susut = $jumlah ? (100 - ($hasil_matang / $jumlah * 100)) : 0;

            return [
                'no' => $i + 1,
                'nama_lauk' => $rincian->resep->nama_resep ?? '-',
                'berat_bahan_baku_per_box' => $jumlah,
                'satuan' => 'kg',
                'jenis' => 'Unggas',
                'hasil_matang_setelah_penyusutan_kg' => $hasil_matang,
                'persentase_penyusutan' => $persen_susut,
            ];
        });
        $laukmasters_rev = DB::table('tb_box_bahan_baku as bb')
            ->join('tb_master_bahan as mb', 'bb.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as s', 'mb.satuan_bahan', '=', 's.id')
            ->where('mb.jenis',2)
            ->select(
                'mb.bahan',
                'bb.isi_per_box',
                's.satuan',
                'bb.hasil_matang',
                'bb.penyusutan'
            )
            ->get();
        /* ----------------------------------------------------------
           Data Kalkulasi – jika Anda masih butuh menampilkan
           “ayam bone” & “ayam giling” secara spesifik,
           ambil 2 baris pertama saja; sisanya abaikan.
           (Boleh dihapus kalau memang tidak perlu lagi.)
        ---------------------------------------------------------- */
        
        $data_rumus_protein_rev = DB::table('tb_rumus_perhitungan_protein')
        ->where('id_menu', $id_menu)
        ->first();
        $data_pack = rincian_sekolah::where('id_menu_harian', $id_menu)->sum('jumlah_penerima_total');
        $jumlahPax = $data_pack;
        $pemorsian = $data_rumus_protein_rev->protein_porsi_a ?? 0;
        $pemorsian_a = $data_rumus_protein_rev->protein_porsi_a ?? 0;
        $pemorsian_b = $data_rumus_protein_rev->protein_porsi_b ?? 0;
        $menu = Menu::find($id_menu);
        
       // $data_bahan_utama = rincian_menu_harian::where('id_menu_harian',)

        $resep1 = MenuBahan::join('tb_resep','tb_menu_bahan.menu_id','tb_resep.id')
        ->join('tb_master_bahan', 'tb_menu_bahan.bahan_id', 'tb_master_bahan.id')
        ->where('tb_menu_bahan.menu_id', $menu->protein)->where('tb_menu_bahan.status_bahan_baku',1)
        ->select('tb_resep.id', 'tb_resep.nama_resep', 'tb_menu_bahan.bahan_id', 'tb_master_bahan.bahan')
        ->first();
        $resep2 = MenuBahan::join('tb_resep', 'tb_menu_bahan.menu_id', 'tb_resep.id')
            ->join('tb_master_bahan', 'tb_menu_bahan.bahan_id', 'tb_master_bahan.id')
            ->where('tb_menu_bahan.menu_id', $menu->protein)->where('tb_menu_bahan.status_bahan_baku', 2)
            ->select('tb_resep.id', 'tb_resep.nama_resep', 'tb_menu_bahan.bahan_id', 'tb_master_bahan.bahan')
            ->first() ;
        
        $jumlah_bahan_1_fix = TbRincianMenuTemp::where('id_menu', $id_menu)
        ->where('id_resep', $menu->protein)->where('id_bahan', $resep1->bahan_id)->first();

        $jumlah_bahan_2_fix = TbRincianMenuTemp::where('id_menu', $id_menu)
            ->where('id_resep', $menu->protein)->where('id_bahan', ($resep2->bahan_id??2000))->first() ?? '-';

        $satuan_bahan_1_fix = TbSatuan::find(($jumlah_bahan_1_fix->id_satuan ?? 2000)) ?? '-';
        $satuan_bahan_2_fix = TbSatuan::find(($jumlah_bahan_2_fix->id_satuan?? 2000)) ;


        $nama1 = $resep1->bahan ?? '-';
        $nama2 = $resep2->bahan ?? '-';
        $mentah1 = $data_rumus_protein_rev->kebutuhan_protein_a ?? 0;
        $mentah2 = $data_rumus_protein_rev->kebutuhan_protein_b ?? 0;

        $matang1 = $data_rumus_protein_rev->kebutuhan_matang_a ;
        $matang2 = $data_rumus_protein_rev->kebutuhan_matang_b;

        $dataKalkulasi = [
            'jumlah_pax_buffer' => $jumlahPax,
            'pemorsian_gram' => $pemorsian,
            'isian_lauk_ayam_bone_nama' => $nama1,
            'isian_lauk_ayam_bone_kg_mentah' => $mentah1,
            'isian_lauk_ayam_bone_kg_matang' => $matang1,
            'isian_lauk_ayam_giling_nama' => $nama2,
            'isian_lauk_ayam_giling_kg_mentah' => $mentah2,
            'isian_lauk_ayam_giling_kg_matang' => $matang2,
            'isian_lauk_total_kg_mentah' => $mentah1 + $mentah2,
            'isian_lauk_total_kg_matang' => $matang1 + $matang2,
            'kebutuhan_bahan_baku_kg' => $data_rumus_protein_rev->kebutuhan_total_mentah ?? 0,
            'kebutuhan_bahan_baku_matang_kali_masak' => $data_rumus_protein_rev->kebutuhan_total_matang ?? 0,
            'kebutuhan_titino_persen' => $data_rumus_protein_rev->jumlah_masak ?? '0%',
            'disamakan_maksimal_selisih_text' => 'Disamakan maksimal selisih ±10%',
            'disamakan_maksimal_selisih_value' => '±10%',
        ];

        /* ----------------------------------------------------------
           Tabel Resep & Penerimaan – tampilkan juga semua baris
        ---------------------------------------------------------- */
        $bahan_utama_1 = TbRincianMenuTemp::where('id_resep', $resep1->menu_id)->where('id_bahan', $resep1->bahan_id)->first();
        $bahan_utama_2 = TbRincianMenuTemp::where('id_resep', ($resep2->menu_id ?? 0))->where('id_bahan', ($resep2->bahan_id??0))->first();

        

        $resepPenerimaan = $rincianLauk->map(function ($item, $i) {
            return (object)[
                'no_resep' => $i + 1,
                'lauk' => $item->resep->nama_resep ?? '-',
                'jumlah' => $item->jumlah ?? 0,
                'satuan_resep' => 'kg',
                'jml_box' => 3,
                'total_box' => 9,
                'isi_box_kg' => number_format(($item->jumlah ?? 0) / 3, 2),
            ];
        });
        
        $pdf = Pdf::loadView('pdf.rekaplauk', compact('resep1','nama1', 'nama2','bahan_utama_1', 'satuan_bahan_1_fix', 'satuan_bahan_2_fix', 'jumlah_bahan_1_fix', 'jumlah_bahan_2_fix','bahan_utama_2','data_rumus_protein_rev','laukMasters', 'dataKalkulasi', 'resepPenerimaan', 'laukmasters_rev'))->setPaper('A4', 'landscape');
        return $pdf->download('rekap.Lauk_' . date('Ymd_His') . '.pdf');
        //return view('pdf.rekaplauk', compact('laukMasters', 'dataKalkulasi', 'resepPenerimaan', 'laukmasters_rev'));
    }
}