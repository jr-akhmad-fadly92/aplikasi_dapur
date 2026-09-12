<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbChecklistLaporanHarianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_checklist_laporan_harian', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_laporan')->unique(); // lph_nomordapur_tahunbulantanggal
            $table->date('tanggal');
            $table->integer('check_bahan_baku')->default(0);
            $table->integer('check_non_bahan_baku')->default(0);
            $table->integer('check_gaji')->default(0);
            $table->integer('check_infrastruktur')->default(0);
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
        Schema::dropIfExists('tb_checklist_laporan_harian');
    }
}
