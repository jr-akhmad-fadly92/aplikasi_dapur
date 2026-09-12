<?php

namespace App\Services;

use App\Models\MasterBahanNutrisi;

class NutrisiService
{
    /**
     * Hitung nutrisi berdasarkan bahan, jumlah, dan BDD
     * 
     * Formula: Nutrisi hasil = (jumlah_bahan × BDD%) / 100 × nilai_nutrisi_per_100g
     * 
     * @param int $bahanId - ID bahan dari master_bahan_nutrisi
     * @param float $jumlahGram - Jumlah bahan dalam gram
     * @param float $bddPercent - BDD dalam persen (default dari bahan, bisa override)
     * @return array Nutrisi terhitung dengan struktur lengkap
     */
    public function hitungNutrisi($bahanId, $jumlahGram, $bddPercent = null)
    {
        $bahan = MasterBahanNutrisi::find($bahanId);
        
        if (!$bahan) {
            return ['error' => 'Bahan tidak ditemukan'];
        }

        // Gunakan BDD dari parameter, fallback ke BDD bahan master
        $bdd = $bddPercent ?? ($bahan->bdd ?? 100);

        // Hitung bahan yang dapat dimakan (BDD)
        $bahanBerisiSebesar = ($jumlahGram * $bdd) / 100;

        // Hitung tiap nutrisi: (jumlah_edible / 100) × nilai_per_100g
        $result = [
            'bahan_id' => $bahanId,
            'nama_bahan' => $bahan->nama_bahan,
            'kode' => $bahan->kode,
            'jumlah_gram' => $jumlahGram,
            'bdd_persen' => $bdd,
            'bahan_yang_dapat_dimakan_gram' => round($bahanBerisiSebesar, 2),
            'nutrisi' => [
                'air' => $this->hitungNilai($bahanBerisiSebesar, $bahan->air),
                'energi' => $this->hitungNilai($bahanBerisiSebesar, $bahan->energi),
                'protein' => $this->hitungNilai($bahanBerisiSebesar, $bahan->protein),
                'lemak' => $this->hitungNilai($bahanBerisiSebesar, $bahan->lemak),
                'karbohidrat' => $this->hitungNilai($bahanBerisiSebesar, $bahan->karbohidrat),
                'serat' => $this->hitungNilai($bahanBerisiSebesar, $bahan->serat),
                'abu' => $this->hitungNilai($bahanBerisiSebesar, $bahan->abu),
                'natrium' => $this->hitungNilai($bahanBerisiSebesar, $bahan->natrium),
                'kalium' => $this->hitungNilai($bahanBerisiSebesar, $bahan->kalium),
                'kalsium' => $this->hitungNilai($bahanBerisiSebesar, $bahan->kalsium),
                'magnesium' => $this->hitungNilai($bahanBerisiSebesar, $bahan->magnesium),
                'fosfor' => $this->hitungNilai($bahanBerisiSebesar, $bahan->fosfor),
                'besi' => $this->hitungNilai($bahanBerisiSebesar, $bahan->besi),
                'seng' => $this->hitungNilai($bahanBerisiSebesar, $bahan->seng),
            ],
            'satuan' => [
                'air' => 'g',
                'energi' => 'kkal',
                'protein' => 'g',
                'lemak' => 'g',
                'karbohidrat' => 'g',
                'serat' => 'g',
                'abu' => 'g',
                'natrium' => 'mg',
                'kalium' => 'mg',
                'kalsium' => 'mg',
                'magnesium' => 'mg',
                'fosfor' => 'mg',
                'besi' => 'mg',
                'seng' => 'mg',
            ],
        ];

        return $result;
    }

    /**
     * Hitung nilai nutrisi individual
     * Rumus: (jumlah_edible / 100) × nilai_per_100g
     * 
     * @param float $bahanEdible - Bahan yang dapat dimakan (setelah BDD)
     * @param float|null $nilaiPer100g - Nilai nutrisi per 100g
     * @return float Nilai nutrisi yang sudah dikalkulasi
     */
    private function hitungNilai($bahanEdible, $nilaiPer100g)
    {
        if ($nilaiPer100g === null || $nilaiPer100g === '' || is_string($nilaiPer100g) && $nilaiPer100g === '-') {
            return 0;
        }

        return round(($bahanEdible / 100) * (float) $nilaiPer100g, 2);
    }

    /**
     * Format tampilan nutrisi untuk response API
     * 
     * @param array $nutrisi - Hasil hitungNutrisi()
     * @return array Formatted untuk display
     */
    public function formatForDisplay($nutrisi)
    {
        if (isset($nutrisi['error'])) {
            return $nutrisi;
        }

        return [
            'bahan' => [
                'id' => $nutrisi['bahan_id'],
                'nama' => $nutrisi['nama_bahan'],
                'kode' => $nutrisi['kode'],
            ],
            'kalkulasi' => [
                'jumlah_gram' => $nutrisi['jumlah_gram'],
                'bdd_persen' => $nutrisi['bdd_persen'],
                'bahan_yang_dapat_dimakan_gram' => $nutrisi['bahan_yang_dapat_dimakan_gram'],
            ],
            'hasil_nutrisi' => array_map(function ($nutrient, $key) use ($nutrisi) {
                return [
                    'nama' => ucfirst(str_replace('_', ' ', $key)),
                    'nilai' => $nutrient,
                    'satuan' => $nutrisi['satuan'][$key] ?? '-',
                ];
            }, $nutrisi['nutrisi'], array_keys($nutrisi['nutrisi'])),
        ];
    }
}
