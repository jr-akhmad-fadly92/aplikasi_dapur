<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbGramasiResepTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_gramasi_resep', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_resep');
            $table->decimal('gramasi_a', 8, 2)->nullable();
            $table->decimal('gramasi_b', 8, 2)->nullable();
            $table->integer('status_bahan')->default(1);
            $table->timestamps();

            // Relasi opsional ke tabel resep
            // $table->foreign('id_resep')->references('id')->on('tb_resep')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_gramasi_resep');
    }
}
