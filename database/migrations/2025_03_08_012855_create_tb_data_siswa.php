<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbDataSiswa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_data_siswa', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_tb_data_sekolah');
            $table->string('tahun_ajaran', 15)->nullable()->comment('tahun ajaran siswa');
            $table->enum('semester', ['ganjil', 'genap'])->nullable()->comment('semester siswa');
            $table->integer('jumlah_a')->comment('jumlah siswa porsi A 150gr')->default(0);
            $table->integer('jumlah_b')->comment('jumlah siswa porsi B 200gr')->default(0);
            $table->boolean('status')->default(1)->comment('status data');
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
        Schema::dropIfExists('tb_data_siswa');
    }
}
