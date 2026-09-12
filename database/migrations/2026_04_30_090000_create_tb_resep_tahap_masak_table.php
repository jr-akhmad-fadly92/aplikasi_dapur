<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_resep_tahap_masak', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_resep');
            $table->unsignedInteger('tahap');
            $table->text('keterangan');
            $table->json('id_bahan')->nullable();
            $table->timestamps();

            $table->foreign('id_resep')->references('id')->on('tb_resep')->onDelete('cascade');
            $table->unique(['id_resep', 'tahap']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_resep_tahap_masak');
    }
};
