<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\MenuGiziHarian;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DatabaseMenuGiziService
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
        $menu = Menu::findOrFail($menuId);

        $reusedNutrition = $this->findReusableNutrition($menu);

        if ($reusedNutrition !== null) {
            $gizi = MenuGiziHarian::updateOrCreate(
                ['id_menu' => $menuId],
                $reusedNutrition['totals']
            );

            return [
                'model' => $gizi,
                'source' => 'database_reuse',
                'missing_ingredients' => [],
                'reused_from_menu_id' => $reusedNutrition['source_menu_id'],
            ];
        }

        $result = $this->calculate($menu);

        $gizi = MenuGiziHarian::updateOrCreate(
            ['id_menu' => $menuId],
            $result['totals']
        );

        return [
            'model' => $gizi,
            'source' => 'database',
            'missing_ingredients' => $result['missing_ingredients'],
        ];
    }

    protected function findReusableNutrition(Menu $menu): ?array
    {
        $matchedMenu = Menu::query()
            ->join('tb_menu_gizi_harian as gizi', 'tb_menu.id', '=', 'gizi.id_menu')
            ->where('tb_menu.id', '!=', $menu->id)
            ->whereRaw('tb_menu.karbohidrat <=> ?', [$menu->karbohidrat])
            ->whereRaw('tb_menu.protein <=> ?', [$menu->protein])
            ->whereRaw('tb_menu.sayur <=> ?', [$menu->sayur])
            ->whereRaw('tb_menu.buah <=> ?', [$menu->buah])
            ->whereRaw('tb_menu.susu <=> ?', [$menu->susu])
            ->whereRaw('COALESCE(LOWER(tb_menu.golongan), "") = ?', [strtolower((string) ($menu->golongan ?? ''))])
            ->orderByDesc('tb_menu.tanggal_kirim')
            ->orderByDesc('tb_menu.id')
            ->select(
                'tb_menu.id as source_menu_id',
                'gizi.energi',
                'gizi.protein',
                'gizi.lemak',
                'gizi.karbohidrat',
                'gizi.serat',
                'gizi.natrium'
            )
            ->first();

        if (!$matchedMenu) {
            return null;
        }

        $totals = [];
        foreach ($this->nutritionFields as $field) {
            $totals[$field] = round((float) ($matchedMenu->{$field} ?? 0), 2);
        }

        return [
            'source_menu_id' => (int) $matchedMenu->source_menu_id,
            'totals' => $totals,
        ];
    }

    public function calculate(Menu $menu): array
    {
        $totals = array_fill_keys($this->nutritionFields, 0.0);
        $missingIngredients = [];
        $golonganKey = $this->resolveGolonganKey((string) ($menu->golongan ?? 'pax_a'));

        foreach ($this->getComponentConfigurations($menu, $golonganKey) as $component) {
            if (empty($component['recipe_id'])) {
                continue;
            }

            $componentResult = $this->calculateComponentTotals(
                (int) $component['recipe_id'],
                (int) $menu->id,
                (string) $component['component'],
                (string) $component['formula_table'],
                $component['portion_field_map']
            );

            foreach ($this->nutritionFields as $field) {
                $totals[$field] += $componentResult['totals'][$field] ?? 0;
            }

            $missingIngredients = array_merge($missingIngredients, $componentResult['missing_ingredients']);
        }

        foreach ($this->nutritionFields as $field) {
            $totals[$field] = round(max((float) $totals[$field], 0), 2);
        }

        return [
            'totals' => $totals,
            'missing_ingredients' => array_values(array_unique($missingIngredients)),
        ];
    }

    protected function getComponentConfigurations(Menu $menu, string $golonganKey): array
    {
        return [
            [
                'component' => 'karbohidrat',
                'recipe_id' => $menu->karbohidrat,
                'formula_table' => 'tb_rumus_perhitungan_karbo',
                'portion_field_map' => [1 => $golonganKey === 'b' ? 'karbo_porsi_b' : 'karbo_porsi_a'],
            ],
            [
                'component' => 'protein',
                'recipe_id' => $menu->protein,
                'formula_table' => 'tb_rumus_perhitungan_protein',
                'portion_field_map' => [1 => $golonganKey === 'b' ? 'protein_porsi_b' : 'protein_porsi_a'],
            ],
            [
                'component' => 'sayur',
                'recipe_id' => $menu->sayur,
                'formula_table' => 'tb_rumus_perhitungan_sayur',
                'portion_field_map' => [
                    1 => 'sayur_porsi_a',
                    2 => 'sayur_porsi_b',
                    4 => 'sayur_porsi_c',
                    5 => 'sayur_porsi_d',
                ],
            ],
            [
                'component' => 'buah',
                'recipe_id' => $menu->buah,
                'formula_table' => 'tb_rumus_perhitungan_buah',
                'portion_field_map' => [1 => $golonganKey === 'b' ? 'buah_porsi_b' : 'buah_porsi_a'],
            ],
            [
                'component' => 'suplemen',
                'recipe_id' => $menu->susu,
                'formula_table' => 'tb_rumus_perhitungan_suplemen',
                'portion_field_map' => [1 => $golonganKey === 'b' ? 'suplemen_porsi_b' : 'suplemen_porsi_a'],
            ],
        ];
    }

    protected function calculateComponentTotals(int $recipeId, int $menuId, string $componentName, string $formulaTable, array $portionFieldMap): array
    {
        $formula = DB::table($formulaTable)
            ->where('id_menu', $menuId)
            ->orderByDesc('id')
            ->first();

        if (!$formula) {
            throw new \RuntimeException('Rumus porsi untuk komponen ' . $componentName . ' belum tersedia di database.');
        }

        $ingredients = DB::table('tb_menu_bahan')
            ->join('tb_master_bahan', 'tb_menu_bahan.bahan_id', '=', 'tb_master_bahan.id')
            ->leftJoin('tb_satuan', 'tb_menu_bahan.id_satuan', '=', 'tb_satuan.id')
            ->where('tb_menu_bahan.menu_id', $recipeId)
            ->select(
                'tb_menu_bahan.bahan_id',
                'tb_master_bahan.bahan',
                'tb_menu_bahan.jumlah',
                'tb_satuan.satuan',
                'tb_menu_bahan.status_bahan_baku'
            )
            ->orderByRaw("FIELD(tb_menu_bahan.status_bahan_baku, 1, 2, 4, 5, 3)")
            ->orderBy('tb_master_bahan.bahan')
            ->get();

        if ($ingredients->isEmpty()) {
            return [
                'totals' => array_fill_keys($this->nutritionFields, 0.0),
                'missing_ingredients' => [],
            ];
        }

        $portionMap = [];
        foreach ($portionFieldMap as $status => $field) {
            $value = (float) ($formula->{$field} ?? 0);
            if ($value > 0) {
                $portionMap[(int) $status] = $value;
            }
        }

        if ($portionMap === []) {
            throw new \RuntimeException('Rumus porsi untuk komponen ' . $componentName . ' belum lengkap di database.');
        }

        $statusTotals = [];
        foreach ($portionMap as $status => $targetPortion) {
            $statusTotals[$status] = (float) $ingredients
                ->where('status_bahan_baku', $status)
                ->sum('jumlah');
        }

        $recipeMainTotal = array_sum($statusTotals);
        $selectedMainTotal = array_sum($portionMap);

        $nutrientRows = $this->getNutrientRowsForIngredients($ingredients->pluck('bahan_id')->map(fn ($id) => (int) $id)->all());
        $totals = array_fill_keys($this->nutritionFields, 0.0);
        $missingIngredients = [];

        foreach ($ingredients as $ingredient) {
            $status = (int) ($ingredient->status_bahan_baku ?? 0);
            $scaling = 1.0;

            if (isset($portionMap[$status])) {
                $recipeStatusTotal = (float) ($statusTotals[$status] ?? 0);
                $scaling = $recipeStatusTotal > 0 ? $portionMap[$status] / $recipeStatusTotal : 0;
            } elseif ($status === 3 && $recipeMainTotal > 0 && $selectedMainTotal > 0) {
                $scaling = $selectedMainTotal / $recipeMainTotal;
            }

            $effectiveAmount = (float) $ingredient->jumlah * $scaling;
            $normalizedAmount = $this->normalizeAmountByUnit($effectiveAmount, (string) ($ingredient->satuan ?? ''));
            $divisor = $this->resolveNutrientDivisor((string) ($ingredient->satuan ?? ''));
            $ingredientNutrients = $nutrientRows[(int) $ingredient->bahan_id] ?? [];

            if ($ingredientNutrients === []) {
                $missingIngredients[] = (string) $ingredient->bahan;
                continue;
            }

            foreach ($this->nutritionFields as $field) {
                $baseAmount = (float) ($ingredientNutrients[$field] ?? 0);
                if ($baseAmount <= 0 || $normalizedAmount <= 0) {
                    continue;
                }

                $totals[$field] += $baseAmount * ($normalizedAmount / $divisor);
            }
        }

        return [
            'totals' => $totals,
            'missing_ingredients' => $missingIngredients,
        ];
    }

    protected function getNutrientRowsForIngredients(array $ingredientIds): array
    {
        if ($ingredientIds === []) {
            return [];
        }

        $kandunganIds = $this->resolveNutrientMasterIds();
        $masterIds = array_filter($kandunganIds);

        if ($masterIds === []) {
            return [];
        }

        $rows = DB::table('tb_role_kandungan_gizi')
            ->whereIn('id_bahan', $ingredientIds)
            ->whereIn('id_kandungan_gizi', $masterIds)
            ->get();

        $idToField = array_flip(array_filter($kandunganIds));
        $grouped = [];

        foreach ($rows as $row) {
            $field = $idToField[(int) $row->id_kandungan_gizi] ?? null;
            if (!$field) {
                continue;
            }

            $grouped[(int) $row->id_bahan][$field] = (float) $row->jumlah;
        }

        return $grouped;
    }

    protected function resolveNutrientMasterIds(): array
    {
        $masters = DB::table('tb_master_kandungan_gizi')->select('id', 'kandungan')->get();

        return [
            'energi' => $this->findMasterId($masters, ['energi']),
            'protein' => $this->findMasterId($masters, ['protein']),
            'lemak' => $this->findMasterId($masters, ['lemak']),
            'karbohidrat' => $this->findMasterId($masters, ['karbohidrat']),
            'serat' => $this->findMasterId($masters, ['serat']),
            'natrium' => $this->findMasterId($masters, ['natrium', 'sodium']),
        ];
    }

    protected function findMasterId(Collection $masters, array $keywords): ?int
    {
        foreach ($masters as $master) {
            $label = strtolower((string) $master->kandungan);
            foreach ($keywords as $keyword) {
                if (str_contains($label, strtolower($keyword))) {
                    return (int) $master->id;
                }
            }
        }

        return null;
    }

    protected function resolveGolonganKey(string $golongan): string
    {
        return in_array(strtolower(trim($golongan)), ['pax_b', 'b'], true) ? 'b' : 'a';
    }

    protected function normalizeAmountByUnit(float $amount, string $unit): float
    {
        $normalizedUnit = strtolower(trim($unit));

        if (in_array($normalizedUnit, ['kg', 'kilogram'], true)) {
            return $amount * 1000;
        }

        if ($normalizedUnit === 'ons') {
            return $amount * 100;
        }

        if (in_array($normalizedUnit, ['mg'], true)) {
            return $amount / 1000;
        }

        if (in_array($normalizedUnit, ['liter', 'ltr', 'l'], true)) {
            return $amount * 1000;
        }

        return $amount;
    }

    protected function resolveNutrientDivisor(string $unit): float
    {
        $normalizedUnit = strtolower(trim($unit));

        if (in_array($normalizedUnit, ['buah', 'pcs', 'pc', 'butir', 'biji', 'pack', 'bungkus', 'sachet', 'botol'], true)) {
            return 1;
        }

        return 100;
    }
}