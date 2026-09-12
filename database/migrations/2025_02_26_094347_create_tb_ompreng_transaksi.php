<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbOmprengTransaksi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_ompreng_transaksi', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tb_menu_id')->nullable()->comment('sementara nullable');
            $table->string('kode_rantang', 50)->nullable();
            $table->string('kode_ompreng', 50)->nullable();
            $table->char('porsi', 1)->nullable()->comment('A = anak, B = dewasa');
            $table->bigInteger('user_id');
            $table->timestamp('tanggal_keluar')->nullable()->comment('tanggal keluar ompreng');
            $table->timestamp('tanggal_masuk')->nullable()->comment('tanggal masuk ompreng');
            $table->boolean('status')->default(0)->comment('status transaksi 0 = keluar, 1 = sudah kembali');
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
        Schema::dropIfExists('tb_ompreng_transaksi');
    }
}
