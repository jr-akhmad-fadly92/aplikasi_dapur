<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbPoBahanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_po_bahan', function (Blueprint $table) {
            $table->id();
            $table->integer('id_po')->nullable();
            $table->integer('id_bahan');
            $table->integer('jumlah_bahan');
            $table->integer('satuan');
            $table->integer('jumlah_po')->nullable();
            $table->integer('id_kontrak')->nullable();
            $table->integer('id_rincian_bahan')->nullable();


            $table->datetime('tanggal_kedatangan')->nullable();
            $table->date('tanggal_digunakan')->nullable();
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
        Schema::dropIfExists('tb_po_bahan');
    }
}
