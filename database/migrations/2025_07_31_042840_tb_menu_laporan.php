<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TbMenuLaporan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laporan_menu_harian', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_kirim');
            $table->string('karbo')->nullable();
            $table->string('protein')->nullable();
            $table->string('sayur')->nullable();
            $table->string('buah')->nullable();
            $table->string('susu')->nullable();
            $table->integer('porsi')->default(0);
            $table->enum('periode', ['1-14', '15-30'])->comment('Pembagian periode tanggal');
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
        Schema::dropIfExists('laporan_menu_harian');
    }
}