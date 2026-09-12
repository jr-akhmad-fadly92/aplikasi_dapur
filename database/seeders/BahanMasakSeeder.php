<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BahanMasak;

class BahanMasakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        BahanMasak::create([
            'bahan_id' => 1,
            'jumlah' => 10,
            'id_satuan' => 1,
            'kategori_bahan' => 'Sayur',
            'status_bahan' => 'Fresh',
        ]);
    }
}
