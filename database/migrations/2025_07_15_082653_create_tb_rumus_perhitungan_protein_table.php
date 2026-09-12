<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbRumusPerhitunganProteinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_rumus_perhitungan_protein', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_menu');

            $table->integer('protein_porsi_a')->nullable();
            $table->integer('protein_porsi_b')->nullable();

            $table->integer('kebutuhan_total_matang')->nullable();

            $table->integer('kebutuhan_protein_a')->nullable();
            $table->integer('kebutuhan_protein_b')->nullable();

            $table->integer('penyusutan_protein_a')->nullable();
            $table->integer('penyusutan_protein_b')->nullable();

            $table->decimal('kebutuhan_matang_a', 10, 2)->nullable();
            $table->decimal('kebutuhan_matang_b', 10, 2)->nullable();
            $table->decimal('kebutuhan_matang_realisasi', 10, 2)->nullable();

            $table->integer('kebutuhan_total_mentah')->nullable();
            $table->integer('kapasitas_tilting')->nullable();
            $table->integer('jumlah_masak')->nullable();
            $table->integer('status')->nullable();
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
        Schema::dropIfExists('tb_rumus_perhitungan_protein');
    }
}
