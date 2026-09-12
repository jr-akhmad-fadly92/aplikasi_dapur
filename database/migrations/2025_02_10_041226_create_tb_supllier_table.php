<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbSupllierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_supllier', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang',30);
            $table->integer('jumlah');
            $table->unsignedBigInteger('id_satuan');
            $table->string('kategori_bahan', 30);
            $table->string('status_bahan', 30);
            
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
        Schema::dropIfExists('tb_supllier_');
    }
}
