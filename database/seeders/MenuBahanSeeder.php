<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuBahan;

class MenuBahanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        MenuBahan::create([
            'menu_id' => 1,
            'bahan_id' => 1,
            'jumlah' => 5,
            'id_satuan' => 1,
        ]);
    }
}
