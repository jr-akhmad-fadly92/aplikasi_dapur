<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Illuminate\Contracts\View\View;
use App\Models\Menu;
use App\Models\MenuGiziHarian;
use App\Models\Resep;
use App\Services\DatabaseMenuGiziService;

class LaporanGiziHarianExport implements FromView, ShouldAutoSize, WithStyles
{
    private $idmenu;

    public function __construct($idmenu)
    {
        $this->idmenu = $idmenu;
    }

    public function view(): View
    {
        $menu = Menu::find($this->idmenu);
        $gizi = MenuGiziHarian::where('id_menu', $this->idmenu)->first();

        if (!$gizi) {
            $gizi = (object)[
                'energi' => 0,
                'protein' => 0,
                'lemak' => 0,
                'karbohidrat' => 0,
                'serat' => 0,
                'natrium' => 0,
            ];
        }

        // Ambil nama resep untuk setiap komponen menu
        $karbohidrat = $menu && $menu->karbohidrat ? Resep::find($menu->karbohidrat) : null;
        $protein = $menu && $menu->protein ? Resep::find($menu->protein) : null;
        $sayur = $menu && $menu->sayur ? Resep::find($menu->sayur) : null;
        $buah = $menu && $menu->buah ? Resep::find($menu->buah) : null;
        $susu = $menu && $menu->susu ? Resep::find($menu->susu) : null;

        $componentBreakdown = $this->buildComponentBreakdown($menu);

        return view('exports.laporan_gizi_harian', [
            'menu' => $menu,
            'gizi' => $gizi,
            'karbohidrat' => $karbohidrat,
            'protein' => $protein,
            'sayur' => $sayur,
            'buah' => $buah,
            'susu' => $susu,
            'componentBreakdown' => $componentBreakdown,
        ]);
    }

    private function zeroTotals(): array
    {
        return [
            'energi' => 0,
            'protein' => 0,
            'lemak' => 0,
            'karbohidrat' => 0,
            'serat' => 0,
            'natrium' => 0,
        ];
    }

    private function buildComponentBreakdown(?Menu $menu): array
    {
        $components = [
            'karbohidrat' => ['label' => 'Karbohidrat', 'menu_field' => 'karbohidrat'],
            'lauk' => ['label' => 'Lauk', 'menu_field' => 'protein'],
            'sayur' => ['label' => 'Sayur', 'menu_field' => 'sayur'],
            'buah' => ['label' => 'Buah', 'menu_field' => 'buah'],
            'suplemen' => ['label' => 'Suplemen', 'menu_field' => 'susu'],
        ];

        if (!$menu) {
            return collect($components)->map(function ($meta) {
                return ['label' => $meta['label'], 'totals' => $this->zeroTotals()];
            })->all();
        }

        $service = new DatabaseMenuGiziService();
        $result = [];

        foreach ($components as $key => $meta) {
            $totals = $this->zeroTotals();

            try {
                $componentMenu = clone $menu;
                $componentMenu->karbohidrat = null;
                $componentMenu->protein = null;
                $componentMenu->sayur = null;
                $componentMenu->buah = null;
                $componentMenu->susu = null;

                $field = $meta['menu_field'];
                $componentMenu->{$field} = $menu->{$field};

                if (!empty($componentMenu->{$field})) {
                    $calculated = $service->calculate($componentMenu);
                    $totals = $calculated['totals'] ?? $totals;
                }
            } catch (\Throwable $e) {
                $totals = $this->zeroTotals();
            }

            $result[$key] = [
                'label' => $meta['label'],
                'totals' => $totals,
            ];
        }

        return $result;
    }

    public function styles($sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['italic' => true]],
            4 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D3D3D3']]],
        ];
    }
}
