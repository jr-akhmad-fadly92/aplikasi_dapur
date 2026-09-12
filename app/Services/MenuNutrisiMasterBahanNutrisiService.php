<?php

namespace App\Services;

use App\Models\MasterBahanNutrisi;
use App\Models\Menu;
use App\Models\MenuGiziHarian;
use Illuminate\Support\Facades\DB;

class MenuNutrisiMasterBahanNutrisiService
{
    protected array $nutritionFields = [
        'energi',
        'protein',
        'lemak',
        'karbohidrat',
        'serat',
        'natrium',
    ];

    public function calculateAndStore(int $menuId): array
    {
        $result = $this->calculate($menuId);

        $gizi = MenuGiziHarian::updateOrCreate(
            ['id_menu' => $menuId],
            $result['totals']
        );

        return [
            'model' => $gizi,
            'source' => 'master_bahan_nutrisi',
            'missing_ingredients' => $result['missing_ingredients'],
            'detail_rows' => $result['detail_rows'],
        ];
    }

    public function calculate(int $menuId): array
    {
        $menu = Menu::findOrFail($menuId);

        $rows = DB::table('rincian_menu_harian as rmh')
            ->join('tb_master_bahan as mb', 'rmh.id_bahan', '=', 'mb.id')
            ->leftJoin('tb_satuan as s', 'rmh.id_satuan', '=', 's.id')
            ->where('rmh.id_menu_harian', $menuId)
            ->select(
                'rmh.id',
                'rmh.id_bahan',
                'rmh.jumlah',
                'rmh.id_satuan',
                'mb.bahan',
                's.satuan as satuan'
            )
            ->orderBy('rmh.id')
            ->get();

        $totals = array_fill_keys($this->nutritionFields, 0.0);
        $missingIngredients = [];
        $detailRows = [];
        $nutrisiService = new NutrisiService();

        foreach ($rows as $row) {
            $masterNutrisi = $this->findMasterNutrisiByBahanName((string) $row->bahan);

            if (!$masterNutrisi) {
                $missingIngredients[] = (string) $row->bahan;
                continue;
            }

            $jumlahGram = $this->normalizeJumlahToGram((float) $row->jumlah, (string) ($row->satuan ?? ''));
            $nutrisi = $nutrisiService->hitungNutrisi($masterNutrisi->id, $jumlahGram, $masterNutrisi->bdd);

            foreach ($this->nutritionFields as $field) {
                $totals[$field] += (float) ($nutrisi['nutrisi'][$field] ?? 0);
            }

            $detailRows[] = [
                'id_rincian' => (int) $row->id,
                'nama_bahan' => (string) $row->bahan,
                'jumlah_asal' => (float) $row->jumlah,
                'satuan' => (string) ($row->satuan ?? ''),
                'jumlah_gram' => round($jumlahGram, 2),
                'master_bahan_nutrisi_id' => (int) $masterNutrisi->id,
                'bdd' => (float) ($masterNutrisi->bdd ?? 100),
                'energi' => $nutrisi['nutrisi']['energi'] ?? 0,
                'protein' => $nutrisi['nutrisi']['protein'] ?? 0,
                'lemak' => $nutrisi['nutrisi']['lemak'] ?? 0,
                'karbohidrat' => $nutrisi['nutrisi']['karbohidrat'] ?? 0,
                'serat' => $nutrisi['nutrisi']['serat'] ?? 0,
                'natrium' => $nutrisi['nutrisi']['natrium'] ?? 0,
            ];
        }

        foreach ($this->nutritionFields as $field) {
            $totals[$field] = round(max((float) $totals[$field], 0), 2);
        }

        return [
            'menu_id' => $menu->id,
            'menu_name' => $menu->menu,
            'totals' => $totals,
            'missing_ingredients' => array_values(array_unique($missingIngredients)),
            'detail_rows' => $detailRows,
        ];
    }

    protected function findMasterNutrisiByBahanName(string $bahanName): ?MasterBahanNutrisi
    {
        $normalized = $this->normalizeText($bahanName);

        if ($normalized === '') {
            return null;
        }

        $exactMatch = MasterBahanNutrisi::query()
            ->whereRaw('LOWER(TRIM(nama_bahan)) = ?', [$normalized])
            ->first();

        if ($exactMatch) {
            return $exactMatch;
        }

        $compactNormalized = str_replace(' ', '', $normalized);

        foreach (MasterBahanNutrisi::query()
            ->whereRaw('LOWER(nama_bahan) LIKE ?', ['%' . $normalized . '%'])
            ->limit(25)
            ->get() as $candidate) {
            $candidateNormalized = $this->normalizeText((string) $candidate->nama_bahan);
            if ($candidateNormalized === $normalized || str_replace(' ', '', $candidateNormalized) === $compactNormalized) {
                return $candidate;
            }
        }

        return null;
    }

    protected function normalizeJumlahToGram(float $jumlah, string $unit): float
    {
        $normalizedUnit = $this->normalizeText($unit);

        if (in_array($normalizedUnit, ['mg'], true)) {
            return $jumlah / 1000;
        }

        return $jumlah;
    }

    protected function normalizeText(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;
        $value = preg_replace('/[^\p{L}\p{N}\s]+/u', '', $value) ?? $value;

        return trim($value);
    }
}