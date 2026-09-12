<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbMenuHariIniTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_menu_hari_ini', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_dapur', 30);
            $table->date('tanggal');
            $table->string('karbohidrat');
            $table->string('protein');
            $table->string('sayur');
            $table->string('buah');
            $table->string('susu');
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
        //
    }
}
