<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterBahanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // Insert data ke tabel 'tb_master_bahan'
        DB::table('tb_master_bahan')->insert([
            'bahan' => 'beras',
            'gramasi' => 1.1,
            'satuan_gudang' => 4,
            'satuan_bahan' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
