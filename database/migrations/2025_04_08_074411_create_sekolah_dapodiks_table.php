<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSekolahDapodiksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sekolah_dapodiks', function (Blueprint $table) {
            $table->id();
            $table->string('_id')->unique();
            $table->string('kode_prop')->nullable();
            $table->string('propinsi')->nullable();
            $table->string('kode_kab_kota')->nullable();
            $table->string('kabupaten_kota')->nullable();
            $table->string('kode_kec')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('npsn')->nullable();
            $table->string('sekolah')->nullable();
            $table->string('bentuk')->nullable();
            $table->string('status')->nullable();
            $table->string('alamat_jalan')->nullable();
            $table->string('lintang')->nullable();
            $table->string('bujur')->nullable();
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
        Schema::dropIfExists('sekolah_dapodiks');
    }
}
