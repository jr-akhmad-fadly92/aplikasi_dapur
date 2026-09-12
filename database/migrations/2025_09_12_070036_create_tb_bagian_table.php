<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTbBagianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_bagian', function (Blueprint $table) {
            $table->id();
            $table->string('id_bagian')->unique();
            $table->string('nama_bagian');
            $table->timestamps();
        });

        // langsung isi data default
        DB::table('tb_bagian')->insert([
            ['id_bagian' => 'BG01', 'nama_bagian' => 'Ka dapur', 'created_at' => now(), 'updated_at' => now()],
            ['id_bagian' => 'BG02', 'nama_bagian' => 'Ahli Gizi', 'created_at' => now(), 'updated_at' => now()],
            ['id_bagian' => 'BG03', 'nama_bagian' => 'Ahli Akuntan', 'created_at' => now(), 'updated_at' => now()],
            ['id_bagian' => 'BG04', 'nama_bagian' => 'Asisten Lapangan', 'created_at' => now(), 'updated_at' => now()],
            ['id_bagian' => 'BG05', 'nama_bagian' => 'Admin dan penerimaan', 'created_at' => now(), 'updated_at' => now()],
            ['id_bagian' => 'BG06', 'nama_bagian' => 'IT, Support dan Administrasi', 'created_at' => now(), 'updated_at' => now()],
            ['id_bagian' => 'BG07', 'nama_bagian' => 'Produksi', 'created_at' => now(), 'updated_at' => now()],
            ['id_bagian' => 'BG08', 'nama_bagian' => 'Pembersihan dan Penataan', 'created_at' => now(), 'updated_at' => now()],
            ['id_bagian' => 'BG09', 'nama_bagian' => 'Security dan maintenance', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_bagian');
    }
}
