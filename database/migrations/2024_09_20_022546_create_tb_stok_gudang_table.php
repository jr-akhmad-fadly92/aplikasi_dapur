<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbStokGudangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_stok_gudang', function (Blueprint $table) {
            $table->id();
            //$table->string('nama_barang', 30);  // Kolom 'nama_barang' dengan tipe varchar (30)
            $table->unsignedBigInteger('nama_barang');  // Kolom 'nama_barang' dengan tipe varchar (30)
            $table->integer('jumlah');  // Kolom 'jumlah' dengan tipe integer
            $table->unsignedBigInteger('id_satuan');  // Kolom 'id_satuan' sebagai Foreign Key
            $table->string('kategori_bahan', 10);  // Kolom 'kategori_bahan' dengan tipe varchar (10)
            $table->string('status_bahan', 10);  // Kolom 'status_bahan' dengan tipe varchar (10)
        
            $table->timestamps();

            //$table->foreign('id_satuan')->references('id')->on('tb_satuan');
 
       
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_stok_gudang');
    }
}
