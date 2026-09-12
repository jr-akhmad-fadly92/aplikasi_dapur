<?php

namespace App\Http\Controllers\pdf;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\rincian_sekolah;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class RekapBerasController extends Controller
{
    public function cetak($id_menu)
    {
        $data_menu      = Menu::find($id_menu);
        $jumlah_penerima_sekolah   = rincian_sekolah::where('id_menu_harian')->sum('jumlah_penerima_total');
        
        $data_rincian   = DB::table('tb_rumus_perhitungan_karbo')
        ->where('id_menu',$id_menu)->first();
        $data = [
            'pax' => $jumlah_penerima_sekolah,
            'pemorsian_a' => $data_rincian->karbo_porsi_a,
            'pemorsian_b' => $data_rincian->karbo_porsi_b,
            
            'kebutuhan' => [
                'beras' => $data_rincian->karbo_kebutuhan_beras_total,
                'tray' => $data_rincian->karbo_kebutuhan_tray,
                'steamer' => $data_rincian->karbo_kebutuhan_steamer,
                'pintu' => $data_rincian->karbo_kebutuhan_pintu_steamer,
                'air' => $data_rincian->karbo_kebutuhan_air,
            ],
            'produksi' => [
                'kg' => $data_rincian->karbo_hasil_produksi_kg,
                'gram' => $data_rincian->karbo_hasil_produksi,
            ],
            'cuci' => $data_rincian->karbo_hitungan_cuci_beras,
            'pencucian' => [12, 0, 0, 0, 0],
            'packing' => [
                [25, 5, 1],  // Pertama
                [0, 2, 2],  // Kedua
                [0, 0, 0],  // Ketiga
                [0, 0, 0],  // Keempat
                [0, 2, 2],  // Kelima
            ],
        ];

        $pdf = Pdf::loadView('pdf.rekapberas', $data)->setPaper('A4', 'portrait');
        return $pdf->download('rekap.beras_' . date('Ymd_His') . '.pdf');
    }
}
