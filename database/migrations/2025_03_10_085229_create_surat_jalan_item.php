<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuratJalanItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surat_jalan_item', function (Blueprint $table) {
            $table->id();
            $table->string('surat_jalan_referensi', 100)->comment('nomor referensi surat jalan');
            $table->bigInteger('rincian_sekolah_id')->comment('ID rincian sekolah untuk menghitung kekurangan yang harus dikirim');
            $table->integer('jumlah_a')->nullable()->comment('jumlah A');
            $table->integer('jumlah_b')->nullable()->comment('jumlah B');  
            $table->integer('jumlah')->nullable()->comment('jumlah total');
            $table->integer('status')->default(0)->comment('status item surat jalan 0=draft, 1=publish, 2=revised, 3=cancel');
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
        Schema::dropIfExists('surat_jalan_item');
    }
}
