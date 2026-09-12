<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class WarehouseTransaksi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('warehouse_transaksi', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_parent')->nullable()->comment('id parent jika pengambilan barang tidak langsung semua');
            $table->bigInteger('id_penerimaan')->nullable();
            $table->string('kode_wadah', 50)->comment('kode wadah');
            $table->string('no_po')->nullable()->comment('nomor PO jika kosong');
            $table->string('nama_barang')->nullable()->comment('nama barang untuk manual');
            $table->dateTime('tanggal_masuk')->comment('tanggal masuk barang auto now');
            $table->dateTime('tanggal_keluar')->nullable()->comment('tanggal keluar barang');
            $table->tinyInteger('status')->default(0)->nullable()->comment('0 digudang 1 keluar');
            $table->integer('jumlah')->comment('jumlah barang');
            $table->bigInteger('id_satuan')->comment('satuan barang');
            $table->dateTime('tanggal_akan_keluar')->nullable()->comment('tanggal akan keluar');
            $table->string('referensi_masuk')->nullable()->comment('referensi masuk jika ingin dikelompokkan');
            $table->string('referensi_keluar')->nullable()->comment('referensi keluar jika ingin dikelompokkan');
            $table->string('lokasi')->nullable()->comment('lokasi penyimpanan');
            $table->integer('jenis')->nullable()->comment('jenis barang bahan baku, peralatan, dsb');
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
        //
        Schema::dropIfExists('warehouse_transaksi');
    }
}
