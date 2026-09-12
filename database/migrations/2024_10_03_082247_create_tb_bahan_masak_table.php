<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbBahanMasakTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //tabel masuk ke warehouse
        Schema::create('tb_bahan_masak', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bahan_id');
            $table->integer('jumlah');
            $table->unsignedBigInteger('id_satuan');
            $table->string('kategori_bahan', 10);
            $table->string('status_bahan', 10);
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
        Schema::dropIfExists('tb_bahan_masak');
    }
}
