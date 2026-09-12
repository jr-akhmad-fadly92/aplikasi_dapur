<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbDataSekolahTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_data_sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sekolah');
            $table->enum('jenjang_sekolah', ['KB/Sederajat', 'TK/Sederajat', 'SD/Sederajat', 'SMP/Sederajat', 'SMA/Sederajat', 'SMK/Sederajat']);
            $table->integer('jumlah_siswa')->nullable()->comment('data jumlah siswa tidak digunakan');
            $table->string('alamat_sekolah');

            $table->string('id_dapur')->nullable();
            $table->integer('jumlah_a')->nullable()->comment('jumlah siswa porsi A 150gr diisi saat input data tb siswa');
            $table->integer('jumlah_b')->nullable()->comment('jumlah siswa porsi B 200gr diisi saat input data tb siswa');
            //$table->json('hari_sekolah')->nullable(); // Menyimpan hari sebagai JSON
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
        Schema::dropIfExists('tb_data_sekolah');
    }
}
