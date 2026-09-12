<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbPenerimaBahanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_penerima_bahan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_dapur', 30);
            $table->date('tanggal');
            $table->integer('jumlah');
            $table->string('nama_supplier');
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
        Schema::dropIfExists('tb_penerima_bahan');
    }
}
