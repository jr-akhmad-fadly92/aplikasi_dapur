<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSekolahAktif extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sekolah_aktif', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_tb_data_sekolah');
            $table->boolean('senin')->default(0)->comment('hari aktif 1=aktif, 0=nonaktif');
            $table->boolean('selasa')->default(0)->comment('hari aktif selasa = 1');
            $table->boolean('rabu')->default(0)->comment('hari aktif rabu = 1');
            $table->boolean('kamis')->default(0)->comment('hari aktif kamis = 1');
            $table->boolean('jumat')->default(0)->comment('hari aktif jumat = 1');
            $table->boolean('sabtu')->default(0)->comment('hari aktif sabtu = 1');
            $table->boolean('minggu')->default(0)->comment('hari aktif minggu = 1');
            $table->boolean('status')->default(1)->comment('status data 1 aktif 0 history');
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
        Schema::dropIfExists('sekolah_aktif');
    }
}
