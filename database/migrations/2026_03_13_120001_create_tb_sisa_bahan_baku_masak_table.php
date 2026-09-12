<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbSisaBahanBakuMasakTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_sisa_bahan_baku_masak', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->unsignedBigInteger('id_menu');
            $table->decimal('sisa_karbo', 12, 2)->default(0);
            $table->decimal('sisa_protein', 12, 2)->default(0);
            $table->decimal('sisa_sayur', 12, 2)->default(0);
            $table->decimal('sisa_buah', 12, 2)->default(0);
            $table->decimal('sisa_susu', 12, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['tanggal', 'id_menu']);
            $table->index('tanggal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_sisa_bahan_baku_masak');
    }
}
