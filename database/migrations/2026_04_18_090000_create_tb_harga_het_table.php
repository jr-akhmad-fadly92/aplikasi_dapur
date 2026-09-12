<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbHargaHetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_harga_het', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_bahan')->unique();
            $table->date('tanggal_update');
            $table->bigInteger('harga_het');
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
        Schema::dropIfExists('tb_harga_het');
    }
}
