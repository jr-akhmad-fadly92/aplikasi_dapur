<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbKbmTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tabel utama KBM
        Schema::create('tb_kbm_sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('tahun_ajaran'); // ex: 2025/2026
            $table->enum('semester', ['ganjil', 'genap']);
            $table->date('tanggal_mulai_kbm');
            $table->date('tanggal_selesai_kbm');
            $table->timestamps();
        });

        // Detail KBM (tanpa foreign key)
        Schema::create('tb_detail_kbm', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kbm'); // hanya angka, tidak ada constraint
            $table->date('tanggal');
            $table->string('keterangan')->nullable();
            $table->enum('status', ['libur', 'masuk'])->default('masuk');
            $table->timestamps();
        });

        // Hari libur
        Schema::create('tb_hari_libur', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('keterangan');
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
        Schema::dropIfExists('tb_hari_libur');
        Schema::dropIfExists('tb_detail_kbm');
        Schema::dropIfExists('tb_kbm_sekolah');
    }
}
