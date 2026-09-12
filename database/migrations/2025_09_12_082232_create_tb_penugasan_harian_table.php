<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbPenugasanHarianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('tb_penugasan_harian', function (Blueprint $table) {
                $table->id('id_penugasan');
                $table->unsignedBigInteger('id_karyawan');
                $table->unsignedBigInteger('id_tugas');
                $table->time('waktu_mulai');
                $table->time('waktu_selesai');
                $table->date('tanggal'); // supaya bisa bedakan hari
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
        Schema::dropIfExists('tb_penugasan_harian');
    }
}
