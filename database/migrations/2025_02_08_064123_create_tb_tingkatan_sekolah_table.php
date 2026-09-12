<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbTingkatanSekolahTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_tingkatan_sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('jenjang_sekolah');
            $table->integer('golongan');
            $table->float('gramasi_nasi');
            $table->float('gramasi_sayur');
            $table->float('gramasi_protein');
            $table->float('gramasi_buah');
            $table->float('gramasi_susu');
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
        Schema::dropIfExists('tb_tingkatan_sekolah');
    }
}
