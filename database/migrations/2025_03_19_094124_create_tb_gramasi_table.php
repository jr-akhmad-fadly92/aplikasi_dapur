<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbGramasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_gramasi', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 5);
            $table->integer('karbohidrat');
            $table->integer('protein');
            $table->integer('sayur');
            $table->integer('buah');
            $table->integer('susu');
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
        Schema::dropIfExists('tb_gramasi');
    }
}
