<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbPaketMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_paket_menu', function (Blueprint $table) {
            $table->id();
            $table->string('paket'); // A atau B
            $table->integer('resep_karbo')->nullable();
            $table->integer('resep_protein')->nullable();
            $table->integer('resep_sayur')->nullable();
            $table->integer('resep_buah')->nullable();
            $table->integer('resep_suplemen')->nullable();

            // berat mentah karbo
            $table->integer('berat_mentah_karbo_a')->nullable();
            $table->integer('berat_mentah_karbo_b')->nullable();

            // berat mentah protein
            $table->integer('berat_mentah_protein_a')->nullable();
            $table->integer('berat_mentah_protein_b')->nullable();

            // berat mentah sayur (maks 4)
            $table->integer('berat_mentah_sayur1_a')->nullable();
            $table->integer('berat_mentah_sayur2_a')->nullable();
            $table->integer('berat_mentah_sayur3_a')->nullable();
            $table->integer('berat_mentah_sayur4_a')->nullable();
            $table->integer('berat_mentah_sayur1_b')->nullable();
            $table->integer('berat_mentah_sayur2_b')->nullable();
            $table->integer('berat_mentah_sayur3_b')->nullable();
            $table->integer('berat_mentah_sayur4_b')->nullable();

            // berat mentah buah
            $table->integer('berat_mentah_buah_a')->nullable();
            $table->integer('berat_mentah_buah_b')->nullable();

            // berat mentah suplemen
            $table->integer('berat_mentah_suplemen_a')->nullable();
            $table->integer('berat_mentah_suplemen_b')->nullable();

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
        Schema::dropIfExists('tb_paket_menu');
    }
}
