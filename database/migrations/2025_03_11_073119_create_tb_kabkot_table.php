<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbKabkotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_kabkot', function (Blueprint $table) {
            $table->id();
            $table->integer('provinsi_id');
            $table->string('kabupaten_kota');
            $table->string('ibukota');
            $table->string('k_bsni');
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
        Schema::dropIfExists('tb_kabkot');
    }
}
