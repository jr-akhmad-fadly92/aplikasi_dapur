<?php

namespace App\Http\Controllers\pdf;

use App\Http\Controllers\Controller;
use App\Models\DataDapur;
use App\Models\Menu;
use App\Models\rincian_sekolah;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class RekapBerasController extends Controller
{
    public function cetak($id_menu)
    {
        $data_menu      = Menu::find($id_menu);
        $dapur          = DataDapur::first();
        $jumlah_penerima_sekolah   = rincian_sekolah::where('id_menu_harian')->sum('jumlah_penerima_total');
        $data_rincian   = DB::table('tb_rumus_perhitungan_karbo')
        ->where('id_menu',$id_menu)->first();

        $pack_utama = $data_rincian->karbo_kebutuhan_beras_total;

        $packWeight = 72; // total berat dari kombinasi 2x25 + 4x5 + 2x1
        $configPack = [25, 5, 1]; // Berat masing-masing kemasan
        $packing = [];

        // Hitung jumlah pack penuh
        $jumlahPackPenuh = intdiv($pack_utama, $packWeight);
        $sisaKg = $pack_utama % $packWeight;

        // Tambahkan pack penuh (2,4,2)
        for ($i = 0; $i < $jumlahPackPenuh; $i++) {
            $packing[] = [2, 4, 2];
        }

        // Hitung sisa kg
        $sisaPack = [0, 0, 0];
        if ($sisaKg >= 25) {
            $sisaPack[0] = 1;
            $sisaKg -= 25;
        }
        while ($sisaKg >= 5) {
            $sisaPack[1]++;
            $sisaKg -= 5;
        }
        while ($sisaKg >= 1) {
            $sisaPack[2]++;
            $sisaKg -= 1;
        }

        // Tambahkan sisa ke packing hanya jika tidak semuanya 0
        if (array_sum($sisaPack) > 0) {
            $packing[] = $sisaPack;
        }

        // Lengkapi sampai 5 baris packing (maksimal 5 slot)
        while (count($packing) < 5) {
            $packing[] = [0, 0, 0];
        }

        // Siapkan array masing-masing pack
        $pack25 = [0, 0, 0, 0, 0];
        $pack5 = [0, 0, 0, 0, 0];
        $pack1 = [0, 0, 0, 0, 0];

        foreach ($packing as $i => $row) {
            $pack25[$i] = $row[0];
            $pack5[$i] = $row[1];
            $pack1[$i] = $row[2];
        }
        $pack_utama = $data_rincian->karbo_kebutuhan_beras_total;
        // Masukkan ke variabel individual
        $pack25_1 = $pack25[0];
        $pack25_2 = $pack25[1];
        $pack25_3 = $pack25[2];
        $pack25_4 = $pack25[3];
        $pack25_5 = $pack25[4];

        $pack5_1 = $pack5[0];
        $pack5_2 = $pack5[1];
        $pack5_3 = $pack5[2];
        $pack5_4 = $pack5[3];
        $pack5_5 = $pack5[4];

        $pack1_1 = $pack1[0];
        $pack1_2 = $pack1[1];
        $pack1_3 = $pack1[2];
        $pack1_4 = $pack1[3];
        $pack1_5 = $pack1[4];


        $jumlah_cuci = $data_rincian->karbo_kebutuhan_beras_total;
        $jumlah_cuci_1 = 0;
        $jumlah_cuci_2 = 0;
        $jumlah_cuci_3 = 0;
        $jumlah_cuci_4 = 0;
        $jumlah_cuci_5 = 0;
        
        if($jumlah_cuci > 72 )
        {
            $jumlah_cuci_1 = 72;
            $jumlah_cuci = $jumlah_cuci - 72;
        }else{
            $jumlah_cuci_1 = $jumlah_cuci;
            $jumlah_cuci = 0;
        }
        if ($jumlah_cuci > 72) {
            $jumlah_cuci_2 = 72;
            $jumlah_cuci = $jumlah_cuci - 72;
        } else {
            $jumlah_cuci_2 = $jumlah_cuci;
            $jumlah_cuci = 0;
        }
        if ($jumlah_cuci > 72) {
            $jumlah_cuci_3 = 72;
            $jumlah_cuci = $jumlah_cuci - 72;
        } else {
            $jumlah_cuci_3 = $jumlah_cuci;
            $jumlah_cuci = 0;
        }
        if ($jumlah_cuci > 72) {
            $jumlah_cuci_4 = 72;
            $jumlah_cuci = $jumlah_cuci - 72;
        } else {
            $jumlah_cuci_4 = $jumlah_cuci;
            $jumlah_cuci = 0;
        }
        if ($jumlah_cuci > 72) {
            $jumlah_cuci_5 = 72;
            $jumlah_cuci = $jumlah_cuci - 72;
        } else {
            $jumlah_cuci_5 = $jumlah_cuci;
            $jumlah_cuci = 0;
        }

        if($data_rincian->karbo_kebutuhan_tray < 12 )
        {
            $pintu_awal = $data_rincian->karbo_kebutuhan_tray % 12;
            $pintu_sisa = 0;
        }else{
            $pintu_awal = $data_rincian->karbo_kebutuhan_tray / 12;
            $pintu_sisa = $data_rincian->karbo_kebutuhan_tray % 12;
        }
        
        $data = [
            'pax' => $jumlah_penerima_sekolah,
            'pemorsian_a' => $data_rincian->karbo_porsi_a,
            'pemorsian_b' => $data_rincian->karbo_porsi_b,
            'pemorsian' => 150,
            'kebutuhan' => [
                'beras' => $data_rincian->karbo_kebutuhan_beras_total,
                'tray' => $data_rincian->karbo_kebutuhan_tray,
                'steamer' => $data_rincian->karbo_kebutuhan_steamer,
                'pintu' => $data_rincian->karbo_kebutuhan_pintu_steamer,
                'air' => $data_rincian->karbo_kebutuhan_air,
            ],
            'dapur' => $dapur,
            'produksi' => [
                'kg' => $data_rincian->karbo_hasil_produksi_kg,
                'gram' => $data_rincian->karbo_hasil_produksi,
            ],
            'cuci' => $data_rincian->karbo_hitungan_cuci_beras,
            'pencucian' => [$jumlah_cuci_1, $jumlah_cuci_2, $jumlah_cuci_3, $jumlah_cuci_4, $jumlah_cuci_5],
            'packing' => [
                [$pack25_1, $pack5_1, $pack1_1],  // Pertama
                [$pack25_2, $pack5_2, $pack1_2],  // Kedua
                [$pack25_3, $pack5_3, $pack1_3],  // Ketiga
                [$pack25_4, $pack5_4, $pack1_4],  // Keempat
                [$pack25_5, $pack5_5, $pack1_5],  // Kelima
            ],
            'pintu_awal' => $pintu_awal,
            'pintu_sisa' => $pintu_sisa
        ];

        $pdf = Pdf::loadView('pdf.rekapberas', $data)->setPaper('A4', 'portrait');
        return $pdf->download('rekap.beras_' . date('Ymd_His') . '.pdf');
    }
}
