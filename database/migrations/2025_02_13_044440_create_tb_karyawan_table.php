<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('id_karyawan',30);
            $table->string('nama', 100);
            $table->string('jabatan', 30);
            $table->date('tanggal_masuk');
            $table->string('status', 10);
            $table->integer('kontak');
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
        Schema::dropIfExists('tb_karyawan');
    }
}
