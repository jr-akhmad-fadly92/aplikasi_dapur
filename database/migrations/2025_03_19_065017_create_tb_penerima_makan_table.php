<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbPenerimaMakanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_penerima_makan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_dapur', 30);
            $table->date('tanggal');
            $table->integer('jumlah_tk_sd_kls3');
            $table->integer('jumlah_sd_kls4_sma');
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
        Schema::dropIfExists('tb_penerima_makan');
    }
}
