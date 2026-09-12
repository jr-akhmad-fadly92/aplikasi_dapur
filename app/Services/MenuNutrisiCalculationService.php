<?php

namespace App\Services;

use App\Models\MenuGiziHarian;
use App\Models\ResepRealisasiAkg;
use Illuminate\Support\Facades\DB;

class MenuNutrisiCalculationService
{
    /**
     * Calculate menu nutrition from tb_resep_realisasi_akg
     * 
     * Formula: (jumlah_bahan_baku_utama / jumlah_siswa * bdd) / 100 * nutrient
     * where:
     * - jumlah_bahan_baku_utama = sum of tb_rincian_menu_temp.jumlah where status_bahan_baku != 3
     * - jumlah_siswa = total student count
     * - bdd = from tb_resep_realisasi_akg
     * - nutrient = energi_kcal, protein_g, lemak_g, karbohidrat_g, serat_g, natrium_mg
     */
    public function calculateAndStoreMenuNutrition(int $menuId, int $jumlahSiswa): MenuGiziHarian
    {
        $menu = DB::table('tb_menu')->where('id', $menuId)->first();
        
        if (!$menu) {
            throw new \Exception("Menu dengan ID $menuId tidak ditemukan");
        }

        $components = [
            'karbo' => $menu->karbohidrat,
            'protein' => $menu->protein,
            'sayur' => $menu->sayur,
            'buah' => $menu->buah,
            'suplemen' => $menu->susu,
        ];

        $totalNutrisi = [
            'energi' => 0,
            'protein' => 0,
            'lemak' => 0,
            'karbohidrat' => 0,
            'serat' => 0,
            'natrium' => 0,
        ];

        // Defensif: jika jumlah siswa 0, set ke 1 untuk mencegah division by zero
        if ($jumlahSiswa <= 0) {
            $jumlahSiswa = 1;
        }

        foreach ($components as $key => $resepId) {
            if (empty($resepId)) {
                continue;
            }

            // Hitung jumlah_bahan_baku_utama untuk komponen ini
            // dari tb_rincian_menu_temp join tb_menu_bahan where status_bahan_baku != 3
            $jumlahBahanBaku = DB::table('tb_rincian_menu_temp as rmt')
                ->join('tb_menu_bahan as mb', function ($join) use ($resepId) {
                    $join->on('rmt.id_bahan', '=', 'mb.bahan_id')
                         ->on('rmt.id_resep', '=', 'mb.menu_id');
                })
                ->where('rmt.id_menu', $menuId)
                ->where('rmt.id_resep', $resepId)
                ->where('mb.status_bahan_baku', '!=', 3)
                ->sum('rmt.jumlah') ?? 0;

            // Ambil nutrisi dari tb_resep_realisasi_akg untuk resep ini
            $realisasiList = ResepRealisasiAkg::where('id_resep', $resepId)
                ->get();

            // Hitung per komponen menggunakan formula
            foreach ($realisasiList as $realisasi) {
                $bdd = (float) ($realisasi->bdd_pct ?? 0);
                if ($bdd <= 0) {
                    $bdd = 100; // Default 100% jika BDD kosong
                }

                $faktor = ($jumlahBahanBaku / $jumlahSiswa * $bdd) / 100;

                $totalNutrisi['energi'] += round($faktor * (float) ($realisasi->energi_kcal ?? 0), 2);
                $totalNutrisi['protein'] += round($faktor * (float) ($realisasi->protein_g ?? 0), 2);
                $totalNutrisi['lemak'] += round($faktor * (float) ($realisasi->lemak_g ?? 0), 2);
                $totalNutrisi['karbohidrat'] += round($faktor * (float) ($realisasi->karbohidrat_g ?? 0), 2);
                $totalNutrisi['serat'] += round($faktor * (float) ($realisasi->serat_g ?? 0), 2);
                $totalNutrisi['natrium'] += round($faktor * (float) ($realisasi->natrium_mg ?? 0), 2);
            }
        }

        // Simpan ke tb_menu_gizi_harian
        $gizi = MenuGiziHarian::updateOrCreate(
            ['id_menu' => $menuId],
            [
                'energi' => round($totalNutrisi['energi'], 2),
                'protein' => round($totalNutrisi['protein'], 2),
                'lemak' => round($totalNutrisi['lemak'], 2),
                'karbohidrat' => round($totalNutrisi['karbohidrat'], 2),
                'serat' => round($totalNutrisi['serat'], 2),
                'natrium' => round($totalNutrisi['natrium'], 2),
            ]
        );

        return $gizi;
    }

    /**
     * Get menu nutrition totals
     */
    public function getMenuNutrition(int $menuId): ?MenuGiziHarian
    {
        return MenuGiziHarian::where('id_menu', $menuId)->first();
    }
}
