<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiswaSekolah extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('siswa_sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('npsn')->nullable()->comment('NPSN Sekolah');
            $table->string('nisn')->nullable()->comment('NISN Siswa');
            $table->string('nama')->nullable()->comment('Nama Siswa');
            $table->string('kelas')->nullable()->comment('Kelas Siswa misal 7A 6B dsb');
            $table->char('jenis_kelamin', 1)->nullable()->comment('Jenis Kelamin Siswa L laki P perempuan');
            $table->string('nama_orangtua')->nullable()->comment('Nama Orang Tua Siswa/wali siswa');
            $table->string('keterangan')->nullable();
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
        Schema::dropIfExists('siswa_sekolah');
    }
}
