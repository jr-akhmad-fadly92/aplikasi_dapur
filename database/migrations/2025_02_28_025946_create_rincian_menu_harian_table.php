<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRincianMenuHarianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rincian_menu_harian', function (Blueprint $table) {
            $table->id();
            $table->integer('id_menu_harian');
            $table->integer('id_resep');
            $table->integer('id_bahan');
            $table->integer('jumlah');
            $table->integer('bumbu');
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
        Schema::dropIfExists('rincian_menu_harian');
    }
}
