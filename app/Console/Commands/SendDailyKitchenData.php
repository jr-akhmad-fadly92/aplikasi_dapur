<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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

class SendDailyKitchenData extends Command
{
    protected $signature = 'send:daily-kitchen-data';
    protected $description = 'Mengirim data harian dapur ke 3 API internal';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tanggal = Carbon::now()->toDateString(); // atau bisa disesuaikan manual

        // --------------------- API 1 ---------------------
        $menu = Menu::where('tanggal_kirim', date('Y-m-d'))->first();
        $dataPertama = DataDapur::first(); // Mengambil satu data paling pertama berdasarkan primary key (biasanya 'id')
        $nama_karbo = Resep::findOrFail($menu->karbohidrat)->nama_resep;
        $nama_protein = Resep::findOrFail($menu->protein)->nama_resep;
        $nama_sayur = Resep::findOrFail($menu->sayur)->nama_resep;
        $nama_buah = Resep::findOrFail($menu->buah)->nama_resep;
        $nama_susu = Resep::findOrFail($menu->susu)->nama_resep;

        $data = DB::table('rincian_sekolah')
            ->selectRaw('SUM(jumlah_penerima_a) as total_a, SUM(jumlah_penerima_b) as total_b')
            ->where('id_menu_harian', $menu->id)
            ->first();

        $api1Data = [
            'nomor_dapur' => $dataPertama->nama_dapur,
            'tanggal' => $menu->tanggal_kirim,
            'jumlah_tk_sd_kls3' => $data->total_a,
            'jumlah_sd_kls4_sma' => $data->total_b
        ];

        $res1 = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post('http://10.8.0.14:8001/api/penerima-makan-gratis', $api1Data);

        $this->info("API 1 Response: " . $res1->status());


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
        $api2Data = [
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
       
        // Kirim data ke API eksternal
        //$response = Http::post('http://10.8.0.14:8001/api/penerimaan-bahan', $api2Data);

        // Kirim ke API 2
        //$response = Http::post('http://10.8.0.14:8001/api/penerimaan-bahan', $api2Data);
         $response = Http::withHeaders([
               
                'Content-Type' => 'application/json',
            ])->post('http://10.8.0.14:8001/api/penerimaan-bahan', $api2Data);
        $this->info("API 2 Response: " . $response->status());

        // --------------------- API 3 ---------------------
       
        
        $api3Data = [
            'nomor_dapur' => $dataPertama->nama_dapur,
            'tanggal' => $menu->tanggal_kirim,
            'karbohidrat' => $nama_karbo,
            'protein' => $nama_protein,
            'sayur' => $nama_sayur,
            'buah' => $nama_buah,
            'susu' => $nama_susu,
        ];

        $res3 = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post('http://10.8.0.14:8001/api/menu-hari-ini', $api3Data);

        $this->info("API 3 Response: " . $res3->status());

        return 0;
    }
}
