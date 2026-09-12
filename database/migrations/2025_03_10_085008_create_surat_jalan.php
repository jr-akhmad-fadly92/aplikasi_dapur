<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuratJalan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surat_jalan', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->comment('ID user yang membuat surat jalan');
            $table->integer('no')->nullable()->comment('nomor urut surat jalan generate ketika publish');
            $table->bigInteger('id_menu_harian')->comment('ID menu harian');
            $table->string('referensi', 100)->comment('nomor referensi pengganti ID');
            $table->string('referensi_parent', 100)->comment('nomor referensiparent jika revisi pengganti ID')->nullable();
            $table->string('no_surat_jalan', 100)->nullable()->comment('nomor surat jalan');
            $table->string('driver', 150)->nullable()->comment('nama driver');
            $table->string('plat_nomor', 50)->nullable()->comment('plat nomor kendaraan');
            $table->dateTime('published_at')->nullable()->comment('tanggal surat jalan');
            $table->integer('status')->default(0)->comment('status surat jalan 0=draft, 1=publish, 2=revised, 3=cancel')->nullable();
            $table->string('keterangan', 500)->nullable()->comment('keterangan surat jalan');
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
        Schema::dropIfExists('surat_jalan');
    }
}
