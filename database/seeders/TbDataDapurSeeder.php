<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TbDataDapurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exists = DB::table('tb_data_dapur')->first();
        if ($exists) {
            return;
        }

        DB::table('tb_data_dapur')->insert([
            'pemilik' => '-unknown-',
            'nama_dapur' => 'Dapur',
            'alamat_dapur' => '-',
            'nomor_dapur' => '001',
            'kecamatan' => '-',
            'kelurahan' => '-',
            'kota' => '-',
            'provinsi' => '-',
            'no_telp' => 0,
            'kepala_dapur' => '-',
            'admin_dapur' => '-',
            'ahli_gizi' => '-',
            'ahli_akuntan' => '-',
            'email' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
