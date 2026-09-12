<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbHistoriMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_histori_menu', function (Blueprint $table) {
            $table->id();
            $table->integer('id_menu');
            $table->string('kode', 5);
            $table->integer('total_porsi');
            $table->integer('hasil_menu_karbohidrat')->nullable();
            $table->integer('hasil_porsi_karbohidrat')->nullable();
            $table->integer('hasil_menu_protein')->nullable();
            $table->integer('hasil_porsi_protein')->nullable();
            $table->integer('hasil_menu_sayur')->nullable();
            $table->integer('hasil_porsi_sayur')->nullable();
            $table->integer('hasil_menu_buah')->nullable();
            $table->integer('hasil_porsi_buah')->nullable();
            $table->integer('hasil_menu_susu')->nullable();
            $table->integer('hasil_porsi_susu')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('tb_histori_menu');
    }
}
