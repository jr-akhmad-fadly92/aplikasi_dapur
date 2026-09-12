<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbDataDapurTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_data_dapur', function (Blueprint $table) {
            $table->id();
            $table->string('pemilik');
            $table->string('nama_dapur');
            $table->string('alamat_dapur');
            $table->string('nomor_dapur');
            $table->string('kecamatan');
            $table->string('kelurahan');
            $table->string('kota');
            $table->string('provinsi');
            $table->integer('no_telp');
            $table->string('kepala_dapur');
            $table->string('admin_dapur');
            $table->string('ahli_gizi');
            $table->string('ahli_akuntan');

            $table->string('email')->nullable();

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
        Schema::dropIfExists('tb_data_dapur');
    }
}
