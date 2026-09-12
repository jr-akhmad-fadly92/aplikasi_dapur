<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbTransaksiWadahTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_transaksi_wadah', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_penerimaan');
            $table->string('qr_code_wadah');
            $table->float('jumlah_berat_sebelum');
            $table->timestamp('tanggal_dan_waktu_masuk');
            $table->float('jumlah_berat_sesudah')->nullable();
            $table->timestamp('tanggal_dan_waktu_sesudah')->nullable();
            $table->timestamp('tanggal_dan_waktu_keluar_gudang')->nullable();
            $table->timestamp('tanggal_dan_waktu_harus_keluar')->nullable();
            $table->tinyInteger('status')->default(0); // 0 = tidak digudang, 1 = masuk gudang , 2 = sudah tidak dipakai
            $table->string('lokasi')->nullable(); // 0 = penyimpanan_kering, 1 = chiller, 2 = freezer
            
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
        Schema::dropIfExists('tb_transaksi_wadah');
    }
}
