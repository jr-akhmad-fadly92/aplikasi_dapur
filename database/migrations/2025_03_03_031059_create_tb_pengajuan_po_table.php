<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbPengajuanPoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_pengajuan_po', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pengajuan', 30);
            $table->integer('total_pengajuan');
            $table->enum('status', ['Pending', 'Acc', 'Batal']);
            $table->string('keterangan');
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
        Schema::dropIfExists('tb_pengajuan_po');
    }
}
