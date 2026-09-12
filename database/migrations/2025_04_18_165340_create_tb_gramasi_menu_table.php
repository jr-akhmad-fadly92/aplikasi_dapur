<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbGramasiMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_gramasi_menu', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_menu');
            // gramasi a
            $table->integer('gramasi_karbo_a')->default(180)->nullable(); // gramasi kelas A
            $table->integer('porsi_tray_a')->default(38)->nullable(); // 38 porsi/tray
            $table->integer('kg_tray_a')->default(3)->nullable(); // 3 kg/tray
            $table->integer('gramasi_lauk_a')->default(100)->nullable(); // 100 gram/porsi
            $table->integer('gramasi_sayur_utama_a')->default(75)->nullable(); // 75 gram
            $table->integer('gramasi_sayur_kedua_a')->default(25)->nullable(); // 25 gram
            $table->integer('gramasi_buah_a')->default(100)->nullable(); // 100 gram
            $table->integer('gramasi_suplemen_a')->default(100)->nullable(); // 100 gram
            // gramasi b
            $table->integer('gramasi_karbo_b')->default(180)->nullable(); // gramasi kelas B
            $table->integer('porsi_tray_b')->default(38)->nullable(); // 38 porsi/tray
            $table->integer('kg_tray_b')->default(3)->nullable(); // 3 kg/tray
            $table->integer('gramasi_lauk_b')->default(100)->nullable(); // 100 gram/porsi
            $table->integer('gramasi_sayur_utama_b')->default(75)->nullable(); // 75 gram
            $table->integer('gramasi_sayur_kedua_b')->default(25)->nullable(); // 25 gram
            $table->integer('gramasi_buah_b')->default(100)->nullable(); // 100 gram
            $table->integer('gramasi_suplemen_b')->default(100)->nullable(); // 100 gram

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
        Schema::dropIfExists('tb_gramasi_menu');
    }
}
