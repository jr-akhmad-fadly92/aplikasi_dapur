<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TbStokGudangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // Ambil ID satuan untuk 'karung' dari tb_satuan
        $idSatuanKarung = DB::table('tb_satuan')->where('satuan', 'karung')->value('id');

        // Masukkan data stok gudang
        DB::table('tb_stok_gudang')->insert([
            'nama_barang' => 1,
            'jumlah' => 20,
            'id_satuan' => 3,
            'kategori_bahan' => 'mentah',
            'status_bahan' => 'aman',
        ]);
    }
}
