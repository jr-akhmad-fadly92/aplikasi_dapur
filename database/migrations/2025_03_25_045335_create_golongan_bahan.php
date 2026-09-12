<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGolonganBahan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('golongan_bahan', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('golongan_id')->comment('id golongan');
            $table->biginteger('bahan_id')->comment('id bahan');
            $table->string('keterangan')->nullable()->comment('keterangan tambahan jika ada');
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
        Schema::dropIfExists('golongan_bahan');
    }
}
