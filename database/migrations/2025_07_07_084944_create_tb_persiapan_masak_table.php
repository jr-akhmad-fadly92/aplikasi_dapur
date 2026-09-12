<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbPersiapanMasakTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_persiapan_masak', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_menu');
            $table->tinyInteger('id_komponen_sehat')->comment('1: karbo, 2: lauk, 3: sayur, 4: buah, 5: suplemen'); // 1-5
            $table->integer('jumlah_sekali_masak');
            $table->integer('berapa_kali_masak');
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
        Schema::dropIfExists('tb_persiapan_masak');
    }
}
