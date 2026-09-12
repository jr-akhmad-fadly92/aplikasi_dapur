<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbPenerimaanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_penerimaan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_barang_po');
            $table->integer('jumlah_datang');
            $table->float('jumlah_berat');
            $table->integer('satuan_berat');
            $table->tinyInteger('status')->comment('0: reject, 1: diterima, 2: kurang 3: masuk gudang' );
            $table->text('keterangan')->nullable();
            $table->string('qr_code_wadah');
            $table->string('nama_penerima', 100);

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
        Schema::dropIfExists('tb_penerimaan');
    }
}
