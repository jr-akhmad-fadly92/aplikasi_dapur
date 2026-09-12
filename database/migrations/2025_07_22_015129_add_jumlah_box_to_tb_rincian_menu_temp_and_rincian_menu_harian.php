<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJumlahBoxToTbRincianMenuTempAndRincianMenuHarian extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rincian_menu_harian', function (Blueprint $table) {
            $table->integer('jumlah_box')->default(1)->after('total_harga');
            $table->integer('id_kontrak')->default(1);
        });
        Schema::table('tb_rincian_menu_temp', function (Blueprint $table) {
            $table->integer('jumlah_box')->default(1)->after('total_harga');
            $table->integer('id_kontrak')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rincian_menu_harian', function (Blueprint $table) {
            $table->dropColumn('jumlah_box');
        });
        Schema::table('tb_rincian_menu_temp', function (Blueprint $table) {
            $table->dropColumn('jumlah_box');
        });
    }
}
