<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbMesinMemasakTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_mesin_memasak', function (Blueprint $table) {
            $table->id();
            $table->string('mesin');
            $table->string('kapasitas');
            $table->integer('status');
            $table->date('tanggal_pengadaan');
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
        Schema::dropIfExists('tb_mesin_memasak');
    }
}
