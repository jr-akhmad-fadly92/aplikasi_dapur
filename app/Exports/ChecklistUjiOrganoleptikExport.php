<?php

namespace App\Exports;

use App\Models\DataDapur;
use App\Models\Menu;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ChecklistUjiOrganoleptikExport implements FromView, ShouldAutoSize
{
    protected $idmenu;

    public function __construct($idmenu)
    {
        $this->idmenu = $idmenu;
    }

    public function view(): View
    {
        $menu = Menu::with([
            'resepKarbohidrat',
            'resepProtein',
            'resepSayur',
            'resepBuah',
            'resepSusu',
        ])->find($this->idmenu);

        $dapur = DataDapur::first();
        $realisasiPax = (int) DB::table('rincian_sekolah')
            ->where('id_menu_harian', $this->idmenu)
            ->sum('jumlah_penerima_total');

        $items = [];

        if ($menu) {
            $components = [
                'Karbohidrat' => $menu->resepKarbohidrat,
                'Lauk' => $menu->resepProtein,
                'Sayur' => $menu->resepSayur,
                'Buah' => $menu->resepBuah,
                'Suplemen' => $menu->resepSusu,
            ];

            foreach ($components as $label => $resep) {
                if ($resep) {
                    $items[] = [
                        'label' => $label,
                        'nama_makanan' => $resep->nama_resep,
                    ];
                }
            }
        }

        return view('exports.checklist_uji_organoleptik', [
            'dapur' => $dapur,
            'menu' => $menu,
            'realisasiPax' => $realisasiPax,
            'items' => $items,
        ]);
    }
}