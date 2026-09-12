<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_menu', function (Blueprint $table) {
            $table->id();
            $table->string('menu', 30);
            $table->foreignId('karbohidrat');
            $table->foreignId('protein');
            $table->foreignId('sayur');
            $table->foreignId('buah');
            $table->foreignId('susu');

                $table->string('nama_pengaju');
                $table->date('tanggal_pengajuan');
                $table->date('tanggal_kirim');
                $table->string('nama_acc')->nullable();
                $table->date('tanggal_acc')->nullable();
                $table->enum('status_pengajuan', ['pending','kirim po', 'approved', 'rejected'])->default('pending');
                $table->string('nomor_pengajuan');
                $table->string('hari_kirim');
                
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
        Schema::dropIfExists('tb_menu');
    }
}
