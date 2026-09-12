<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbBantuBahanPoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_bantu_bahan_po', function (Blueprint $table) {
            $table->id();
            $table->integer('id_menu_harian');
            $table->integer('id_resep');
            $table->integer('id_bahan');
            $table->integer('jumlah');
            $table->integer('bumbu');
            $table->integer('id_po')->nullable();
            $table->integer('jumlah_bahan');
            $table->integer('satuan');
            $table->integer('jumlah_po')->nullable();
            $table->datetime('tanggal_kedatangan')->nullable();
            $table->timestamp('tanggal_digunakan')->nullable();
            $table->integer('id_kontrak')->nullable();



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
        Schema::dropIfExists('tb_bantu_bahan_po');
    }
}
