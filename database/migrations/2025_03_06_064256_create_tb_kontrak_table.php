<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbKontrakTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_kontrak', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kontrak');
            $table->integer('id_supplier');
            $table->date('awal_kontrak');
            $table->date('akhir_kontrak');
            $table->string('cara_pembayaran');
            $table->string('nomor_rekening_pembayaran')->nullable();
            $table->string('bank')->nullable();
            $table->string('nama_rekening')->nullable();
            $table->string('status');
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
        Schema::dropIfExists('tb_kontrak');
    }
}
