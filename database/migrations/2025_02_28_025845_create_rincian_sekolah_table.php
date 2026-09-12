<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRincianSekolahTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rincian_sekolah', function (Blueprint $table) {
            $table->id();
            $table->integer('id_menu_harian');
            $table->integer('id_sekolah');
            $table->integer('jumlah_penerima_total');
            $table->integer('jumlah_penerima_a');
            $table->integer('jumlah_penerima_b');
            $table->integer('status');
            
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
        Schema::dropIfExists('rincian_sekolah');
    }
}
