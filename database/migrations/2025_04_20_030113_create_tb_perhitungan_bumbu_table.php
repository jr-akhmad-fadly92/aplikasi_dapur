<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbPerhitunganBumbuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_perhitungan_bumbu', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_menu_bahan');
            $table->integer('pembagi')->default(20000);
            $table->integer('pengali')->default(500);
            $table->string('keterangan', 100)->default(''); // max 100 karakter
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
        Schema::dropIfExists('tb_perhitungan_bumbu');
    }
}
