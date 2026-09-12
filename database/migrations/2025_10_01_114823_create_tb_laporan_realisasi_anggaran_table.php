<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbLaporanRealisasiAnggaranTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_laporan_realisasi_anggaran', function (Blueprint $table) {
            $table->id();
            $table->string('id_laporan',50)->nullable(); // tanpa foreign key
            $table->bigInteger('bgn')->default(0);
            $table->bigInteger('yayasan')->default(0);
            $table->bigInteger('pihak_lain')->default(0);
            $table->date('periode_awal');
            $table->date('periode_akhir');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_laporan_realisasi_anggaran');
    }
}
