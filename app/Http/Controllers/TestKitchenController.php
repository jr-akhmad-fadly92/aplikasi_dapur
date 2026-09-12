<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

use App\Models\Menu;
use App\Models\Resep;
use App\Models\rincian_menu_harian;
use App\Models\MenuBahan;
use App\Models\rincian_sekolah;
use App\Models\DataSekolah;
use App\Models\DataDapur;
use App\Models\TbPoBahan;
use App\Models\TingkatanSekolah;
use Illuminate\Support\Facades\DB;

class TestKitchenController extends Controller
{
    public function kirim()
    {
        // Data yang akan dikirim
        $tanggal = Carbon::now()->toDateString(); // atau bisa disesuaikan manual

        // --------------------- API 1 ---------------------
        $menu = Menu::where('tanggal_kirim', date('Y-m-d'))->first();
        $dataPertama = DataDapur::first(); // Mengambil satu data paling pertama berdasarkan primary key (biasanya 'id')
        $nama_karbo = Resep::findOrFail($menu->karbohidrat)->nama_resep;
        $nama_protein = Resep::findOrFail($menu->protein)->nama_resep;
        $nama_sayur = Resep::findOrFail($menu->sayur)->nama_resep;
        $nama_buah = Resep::findOrFail($menu->buah)->nama_resep;
        $nama_susu = Resep::findOrFail($menu->susu)->nama_resep;
        $data = [
            "nomor_dapur" => $dataPertama->nama_dapur,
            "tanggal" => $menu->tanggal_kirim,
            "karbohidrat" => $nama_karbo,
            "protein" => $nama_protein,
            "sayur" => $nama_sayur,
            "buah" => $nama_buah,
            "susu" => $nama_susu
        ];

        // Kirim ke API penerima
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('http://10.8.0.14:8001/api/menu-hari-ini', $data);
        // --------------------- API 2 --------------------- 
        $today = date('Y-m-d');

        $bahanMasukList = DB::table('tb_po_bahan')
            ->join('tb_master_bahan', 'tb_po_bahan.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
            ->select(
                'tb_po_bahan.tanggal_kedatangan',
                'tb_master_bahan.bahan',
                'tb_po_bahan.jumlah_bahan',
                'tb_satuan.satuan',
            )
            ->whereDate('tb_po_bahan.tanggal_kedatangan', $menu->tanggal_kirim)
            ->get();
        $data = [
            'data' => $bahanMasukList->map(function ($item) use ($dataPertama) {
                return [
                    'nomor_dapur' => $dataPertama->nama_dapur,
                    'tanggal' => date('Y-m-d'),
                    'nama_bahan' => $item->bahan,
                    'jumlah' => (string)  $item->jumlah_bahan,
                    'satuan' => $item->satuan,
                    'nama_supplier' => 'koperasi',
                ];
            })->toArray()
        ];

        // Kirim ke API penerima
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('http://10.8.0.14:8001/api/penerimaan-bahan', $data);
        // --------------------- API 2 --------------------- 
        $data = DB::table('rincian_sekolah')
            ->selectRaw('SUM(jumlah_penerima_a) as total_a, SUM(jumlah_penerima_b) as total_b')
            ->where('id_menu_harian', $menu->id)
            ->first();
        $data = [
            'nomor_dapur' => $dataPertama->nama_dapur,
            'tanggal' => $menu->tanggal_kirim,
            'jumlah_tk_sd_kls3' => $data->total_a,
            'jumlah_sd_kls4_sma' => $data->total_b
        ];

        // Kirim ke API penerima
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('http://10.8.0.14:8001/api/penerima-makan-gratis', $data);
        // Ambil hasil response dari server penerima
        return $response->json();
    }
}
