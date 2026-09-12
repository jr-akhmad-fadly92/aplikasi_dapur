<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbRumusPerhitunganKarboTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_rumus_perhitungan_karbo', function (Blueprint $table) {
            $table->id();
            // Kolom relasi id_menu (TANPA foreign key constraint)
            // Ini hanya kolom integer biasa, bukan constraint.
            $table->unsignedBigInteger('id_menu');

            // Jumlah porsi A
            $table->integer('karbo_porsi_a');

            // Jumlah porsi B
            $table->integer('karbo_porsi_b');

            // Kebutuhan beras porsi A (gram atau unit)
            $table->integer('karbo_kebutuhan_beras_a');

            // Kebutuhan beras porsi B
            $table->integer('karbo_kebutuhan_beras_b');

            // Total kebutuhan beras (A+B)
            $table->integer('karbo_kebutuhan_beras_total');

            // Jumlah tray yang diperlukan
            $table->integer('karbo_kebutuhan_tray');

            // Jumlah steamer yang digunakan
            $table->integer('karbo_kebutuhan_steamer');

            // Jumlah pintu steamer
            $table->integer('karbo_kebutuhan_pintu_steamer');

            // Kebutuhan air (2 digit di belakang koma)
            $table->decimal('karbo_kebutuhan_air', 8, 2);

            // Hasil produksi karbohidrat (misalnya porsi atau volume)
            $table->decimal('karbo_hasil_produksi', 8, 2);

            // Hasil produksi dalam kg
            $table->decimal('karbo_hasil_produksi_kg', 8, 2);

            // Hitungan cuci beras (berapa kali proses cuci)
            $table->integer('karbo_hitungan_cuci_beras');
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
        Schema::dropIfExists('tb_rumus_perhitungan_karbo');
    }
}
