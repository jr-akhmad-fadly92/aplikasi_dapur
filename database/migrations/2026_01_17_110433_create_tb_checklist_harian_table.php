<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbChecklistHarianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_checklist_harian', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->nullable();
            $table->integer('nomor_dokumen')->nullable();
            $table->string('nama_dokumen')->nullable();
            $table->string('kode_form')->nullable();
            $table->enum('status', ['ada', 'tidak', 'tidak_lengkap'])->nullable();
            $table->date('tanggal_cek')->nullable();
            $table->string('ttd')->nullable();
            $table->text('catatan')->nullable();
            $table->string('dilaporan_oleh')->nullable();
            $table->string('diverifikasi_oleh')->nullable();
            $table->string('didistribusi_oleh')->nullable();
            $table->integer('pax_a')->nullable();
            $table->integer('pax_b')->nullable();
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
        Schema::dropIfExists('tb_checklist_harian');
    }
}
