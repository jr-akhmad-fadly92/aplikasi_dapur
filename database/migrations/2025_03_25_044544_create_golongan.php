<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGolongan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('golongan', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parent_id')->nullable()->comment('id golongan parent jika ada');
            $table->bigInteger('no')->nullable()->default(0)->comment('nomor urut tiap golongan');
            $table->string('golongan_path')->nullable()->comment('path golongan');
            $table->string('golongan')->nullable()->comment('nama golongan');
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
        Schema::dropIfExists('golongan');
    }
}
