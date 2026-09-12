<?php

namespace App\Services;

use App\Models\MasterBahanNutrisi;
use App\Models\ResepRealisasiAkg;

class ResepAkgRealisasiService
{
    public function calculateRow(MasterBahanNutrisi $masterBahan, float $jumlahGram, ?float $bddPct = null): array
    {
        $bdd = $bddPct ?? (float) ($masterBahan->bdd ?? 0);
        
        // Formula: nutrient = (jumlah_gram / bdd) * nutrient_per_100g
        // Normalize BDD percentage to decimal (e.g., 100 -> 1.0)
        $bddDecimal = $bdd / 100;
        if ($bddDecimal <= 0) {
            $bddDecimal = 1; // Default to 100% if BDD is 0 or invalid
        }
        $faktor = $jumlahGram / $bddDecimal / 100;

        return [
            'jumlah_gram' => round($jumlahGram, 2),
            'bdd_pct' => round($bdd, 2),
            'energi_kcal' => round($faktor * (float) ($masterBahan->energi ?? 0), 2),
            'protein_g' => round($faktor * (float) ($masterBahan->protein ?? 0), 2),
            'lemak_g' => round($faktor * (float) ($masterBahan->lemak ?? 0), 2),
            'karbohidrat_g' => round($faktor * (float) ($masterBahan->karbohidrat ?? 0), 2),
            'serat_g' => round($faktor * (float) ($masterBahan->serat ?? 0), 2),
            'natrium_mg' => round($faktor * (float) ($masterBahan->natrium ?? 0), 2),
        ];
    }

    public function storeFromMasterBahan(int $resepId, MasterBahanNutrisi $masterBahan, float $jumlahGram, ?float $bddPct = null): ResepRealisasiAkg
    {
        $payload = $this->calculateRow($masterBahan, $jumlahGram, $bddPct);

        return ResepRealisasiAkg::updateOrCreate(
            [
                'id_resep' => $resepId,
                'id_master_bahan_nutrisi' => $masterBahan->id,
            ],
            $payload
        );
    }
}