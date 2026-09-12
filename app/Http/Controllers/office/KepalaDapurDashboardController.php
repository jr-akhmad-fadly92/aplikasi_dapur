<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KepalaDapurDashboardController extends Controller
{
    public function index(Request $request)
    {
        $header = 'Dashboard Kepala Dapur';
        $selectedDate = $request->get('tanggal', Carbon::today()->toDateString());

        $menuRows = DB::table('tb_menu as m')
            ->leftJoin('tb_resep as r_karbo', 'm.karbohidrat', '=', 'r_karbo.id')
            ->leftJoin('tb_resep as r_protein', 'm.protein', '=', 'r_protein.id')
            ->leftJoin('tb_resep as r_sayur', 'm.sayur', '=', 'r_sayur.id')
            ->leftJoin('tb_resep as r_buah', 'm.buah', '=', 'r_buah.id')
            ->leftJoin('tb_resep as r_susu', 'm.susu', '=', 'r_susu.id')
            ->leftJoin('tb_menu_gizi_harian as g', 'm.id', '=', 'g.id_menu')
            ->whereDate('m.tanggal_kirim', $selectedDate)
            ->orderBy('m.id', 'asc')
            ->select(
                'm.id',
                'm.menu',
                'm.golongan',
                'r_karbo.nama_resep as karbohidrat',
                'r_protein.nama_resep as protein',
                'r_sayur.nama_resep as sayur',
                'r_buah.nama_resep as buah',
                'r_susu.nama_resep as suplemen',
                'g.energi',
                'g.protein as protein_gizi',
                'g.lemak',
                'g.karbohidrat as karbohidrat_gizi',
                'g.serat',
                'g.natrium'
            )
            ->get();

        $menuIds = $menuRows->pluck('id')->filter()->values()->all();

        $sekolahPerMenu = [];
        if (!empty($menuIds)) {
            $sekolahRows = DB::table('rincian_sekolah as rs')
                ->leftJoin('tb_data_sekolah as ds', 'rs.id_sekolah', '=', 'ds.id')
                ->whereIn('rs.id_menu_harian', $menuIds)
                ->where('rs.status', 1)
                ->select(
                    'rs.id_menu_harian',
                    'ds.nama_sekolah',
                    'ds.kecamatan as wilayah',
                    'rs.jumlah_penerima_total as porsi'
                )
                ->orderBy('rs.id_menu_harian', 'asc')
                ->orderBy('ds.nama_sekolah', 'asc')
                ->get();

            $sekolahPerMenu = $sekolahRows
                ->groupBy('id_menu_harian')
                ->map(function ($rows) {
                    return $rows->map(function ($row) {
                        return [
                            'nama' => $row->nama_sekolah ?: '-',
                            'wilayah' => $row->wilayah ?: '-',
                            'porsi' => (int) ($row->porsi ?? 0),
                        ];
                    })->values()->all();
                })
                ->toArray();
        }

        $menuHarianList = $menuRows->map(function ($row) {
            $menuId = (string) $row->id;

            return [
                'id' => $menuId,
                'nama_menu' => $row->menu ?: '-',
                'golongan' => $row->golongan ?: '-',
                'karbohidrat' => $row->karbohidrat ?: '-',
                'protein' => $row->protein ?: '-',
                'sayur' => $row->sayur ?: '-',
                'buah' => $row->buah ?: '-',
                'suplemen' => $row->suplemen ?: '-',
                'sekolah_penerima' => $sekolahPerMenu[$row->id] ?? [],
                'akg' => [
                    'energi_kcal' => (float) ($row->energi ?? 0),
                    'protein_gram' => (float) ($row->protein_gizi ?? 0),
                    'lemak_gram' => (float) ($row->lemak ?? 0),
                    'karbo_gram' => (float) ($row->karbohidrat_gizi ?? 0),
                    'serat_gram' => (float) ($row->serat ?? 0),
                    'natrium_mg' => (float) ($row->natrium ?? 0),
                ],
            ];
        })->values()->all();

        if (empty($menuHarianList)) {
            $menuHarianList[] = [
                'id' => '-',
                'nama_menu' => 'Belum ada menu',
                'golongan' => '-',
                'karbohidrat' => '-',
                'protein' => '-',
                'sayur' => '-',
                'buah' => '-',
                'suplemen' => '-',
                'sekolah_penerima' => [],
                'akg' => [
                    'energi_kcal' => 0,
                    'protein_gram' => 0,
                    'lemak_gram' => 0,
                    'karbo_gram' => 0,
                    'serat_gram' => 0,
                    'natrium_mg' => 0,
                ],
            ];
        }

        $bahanDatangHariIni = DB::table('tb_po_bahan as pb')
            ->leftJoin('tb_master_bahan as b', 'pb.id_bahan', '=', 'b.id')
            ->leftJoin('tb_po as po', 'pb.id_po', '=', 'po.id')
            ->leftJoin('tb_kontrak as k', 'po.id_kontrak', '=', 'k.id')
            ->leftJoin('tb_supplier as s', 'k.id_supplier', '=', 's.id')
            ->leftJoin('tb_satuan as st', 'b.satuan_bahan', '=', 'st.id')
            ->whereDate('pb.tanggal_kedatangan', $selectedDate)
            ->orderBy('pb.tanggal_kedatangan', 'asc')
            ->select(
                'b.bahan as nama_bahan',
                's.nama_supplier',
                'pb.jumlah_po',
                'st.satuan',
                'pb.tanggal_kedatangan'
            )
            ->get()
            ->map(function ($row) {
                $jumlahPo = (float) ($row->jumlah_po ?? 0);
                $satuan = $row->satuan ?: '';
                $qtyLabel = number_format($jumlahPo, 0, ',', '.');
                if (!empty($satuan)) {
                    $qtyLabel .= ' ' . $satuan;
                }

                return [
                    'bahan' => $row->nama_bahan ?: '-',
                    'supplier' => $row->nama_supplier ?: '-',
                    'qty' => $qtyLabel,
                    'eta' => !empty($row->tanggal_kedatangan)
                        ? Carbon::parse($row->tanggal_kedatangan)->format('H:i')
                        : '-',
                ];
            })
            ->values()
            ->all();

        if (empty($bahanDatangHariIni)) {
            $bahanDatangHariIni[] = [
                'bahan' => '-',
                'supplier' => '-',
                'qty' => '0',
                'eta' => '-',
            ];
        }

        $dana = [
            'saldo_saat_ini' => 82500000,
            'pengeluaran_hari_ini' => 13750000,
            'sisa_setelah_pengeluaran' => 68750000,
        ];

        $hetTerupdate = DB::table('tb_harga_het as h')
            ->join('tb_master_bahan as b', 'h.id_bahan', '=', 'b.id')
            ->select(
                'h.id_bahan',
                'b.bahan as nama_bahan',
                'h.tanggal_update',
                'h.harga_het'
            )
            ->orderBy('h.tanggal_update', 'desc')
            ->orderBy('b.bahan', 'asc')
            ->get();

        return view('office.dashboard_kepala_dapur', compact(
            'header',
            'selectedDate',
            'menuHarianList',
            'bahanDatangHariIni',
            'dana',
            'hetTerupdate'
        ));
    }
}
