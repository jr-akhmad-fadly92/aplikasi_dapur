<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbPoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_po', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_po', 30);
            $table->date('tanggal_po');
            $table->string('status_po');
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_approve')->nullable();
            $table->integer('id_kontrak');
            $table->integer('manual')->default(0);
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
        Schema::dropIfExists('tb_po');
    }
}
