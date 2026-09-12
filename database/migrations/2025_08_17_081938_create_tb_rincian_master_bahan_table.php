<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbRincianMasterBahanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_rincian_master_bahan', function (Blueprint $table) {
            $table->id(); // kolom id (primary key, auto increment)
            $table->unsignedBigInteger('id_bahan'); // relasi ke master bahan
            $table->integer('jumlah')->default(0);
            $table->integer('satuan')->default(0);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');

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
        Schema::dropIfExists('tb_rincian_master_bahan');
    }
}
