<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbRincianKontrakTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_rincian_kontrak', function (Blueprint $table) {
            $table->id();
            $table->integer('id_kontrak');
            $table->integer('id_bahan');
            $table->string('merek_bahan');
            $table->integer('harga_bahan');
            $table->integer('jumlah_bahan');
            $table->string('satuan_bahan');
            $table->integer('status');//status masih dipakai atau tidak
            $table->string('kemasan');

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
        Schema::dropIfExists('tb_rincian_kontrak');
    }
}
