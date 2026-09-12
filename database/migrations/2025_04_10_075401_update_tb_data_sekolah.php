<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTbDataSekolah extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_data_sekolah', function (Blueprint $table) {
            //
            //$table->id();
            $table->string('kode_prop')->nullable();
            $table->string('propinsi')->nullable();
            $table->string('kode_kab_kota')->nullable();
            $table->string('kabupaten_kota')->nullable();
            $table->string('kode_kec')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('npsn')->nullable();
            $table->string('status')->nullable()->comment('n negeri, s swasta');
            $table->string('lintang')->nullable();
            $table->string('bujur')->nullable();
            $table->string('bentuk')->nullable()->comment('bentuk sekolah KB, TK, SD, SMP, SMA, SMK');
            $table->integer('jarak')->nullable()->comment('jarak sekolah dari dapur dalam meter');
            $table->string('kelurahan')->nullable()->comment('kelurahan sekolah');
            $table->string('email')->nullable()->comment('email sekolah');
            $table->string('kepala_sekolah')->nullable()->comment('nama kepala sekolah');
            $table->string('no_telp')->nullable()->comment('nomor telepon sekolah');
            $table->integer('waktu_tempuh')->nullable()->comment('waktu tempuh sekolah dari dapur dalam menit');

            //$table->string('id_dapur')->nullable();
            //$table->integer('jumlah_a')->nullable()->comment('jumlah siswa porsi A 150gr diisi saat input data tb siswa');
            //$table->integer('jumlah_b')->nullable()->comment('jumlah siswa porsi B 200gr diisi saat input data tb siswa');
            //$table->json('hari_sekolah')->nullable(); // Menyimpan hari sebagai JSON
            //$table->timestamps();
            //$table->renameColumn('jenjang_sekolah','bentuk');
            //$table->string('bentuk')->change();
            //$table->enum('jenjang_sekolah', ['KB/Sederajat', 'TK/Sederajat', 'SD/Sederajat', 'SMP/Sederajat', 'SMA/Sederajat', 'SMK/Sederajat']);
            //$table->integer('jumlah_siswa')->nullable()->comment('data jumlah siswa tidak digunakan');
            //$table->string('alamat_sekolah');
            //$table->renameColumn('alamat_sekolah','alamat_jalan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_data_sekolah', function (Blueprint $table) {
            //
        });
    }
}
