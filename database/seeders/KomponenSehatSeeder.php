<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KomponenSehat;

class KomponenSehatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $data = [
            ['komponen' => 'karbohidrat'],
            ['komponen' => 'protein'],
            ['komponen' => 'sayur'],
            ['komponen' => 'buah'],
            ['komponen' => 'susu'],
        ];

        foreach ($data as $item) {
            KomponenSehat::create($item);
        }
    }
}
