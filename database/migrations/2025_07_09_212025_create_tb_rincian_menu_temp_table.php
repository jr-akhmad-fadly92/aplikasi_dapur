<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbRincianMenuTempTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_rincian_menu_temp', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_menu');
            $table->unsignedBigInteger('id_resep');
            $table->unsignedBigInteger('id_bahan');
            $table->integer('jumlah')->default(0);
            $table->integer('bumbu')->default(0);
            $table->integer('harga')->default(0);
            $table->integer('total_harga')->default(0);
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
        Schema::dropIfExists('tb_rincian_menu_temp');
    }
}
