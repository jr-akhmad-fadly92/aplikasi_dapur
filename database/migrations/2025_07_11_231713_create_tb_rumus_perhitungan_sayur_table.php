<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbRumusPerhitunganSayurTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_rumus_perhitungan_sayur', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_menu');

            $table->integer('sayur_porsi_a')->nullable();
            $table->integer('sayur_porsi_b')->nullable();
            $table->integer('sayur_porsi_c')->nullable();
            $table->integer('sayur_porsi_d')->nullable();

            $table->integer('kebutuhan_total_matang')->nullable();

            $table->integer('kebutuhan_sayur_a')->nullable();
            $table->integer('kebutuhan_sayur_b')->nullable();
            $table->integer('kebutuhan_sayur_c')->nullable();
            $table->integer('kebutuhan_sayur_d')->nullable();

            $table->integer('penyusutan_sayur_a')->nullable();
            $table->integer('penyusutan_sayur_b')->nullable();
            $table->integer('penyusutan_sayur_c')->nullable();
            $table->integer('penyusutan_sayur_d')->nullable();

            $table->decimal('kebutuhan_matang_a', 10, 2)->nullable();
            $table->decimal('kebutuhan_matang_b', 10, 2)->nullable();
            $table->decimal('kebutuhan_matang_c', 10, 2)->nullable();
            $table->decimal('kebutuhan_matang_d', 10, 2)->nullable();
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
        Schema::dropIfExists('tb_rumus_perhitungan_sayur');
    }
}
