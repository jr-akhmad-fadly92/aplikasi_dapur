<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbKasKecilTransaksiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_kas_kecil_transaksi', function (Blueprint $table) {
            $table->id();
            $table->datetime('tanggal');
            $table->enum('jenis_transaksi', ['masuk', 'keluar']);
            $table->unsignedBigInteger('master_bahan_id')->nullable();
            $table->string('deskripsi')->nullable();
            $table->decimal('jumlah', 15, 2);
            // Ubah tanggal menjadi datetime
            $table->dateTime('tanggal')->change();

            // Tambah nama_karyawan dan nomor_transaksi
            $table->string('nama_karyawan')->nullable();
            $table->string('nomor_transaksi')->nullable();

            // Tambah status dan id_parent
            $table->tinyInteger('status')->default(1)->comment('1: acc, 0: revisi');
            $table->unsignedBigInteger('id_parent')->nullable();
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
        Schema::dropIfExists('tb_kas_kecil_transaksi');
    }
}
