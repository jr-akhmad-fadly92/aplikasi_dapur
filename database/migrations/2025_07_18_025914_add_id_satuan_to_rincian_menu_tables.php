<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdSatuanToRincianMenuTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rincian_menu_harian', function (Blueprint $table) {
            $table->unsignedBigInteger('id_satuan')->after('total_harga')->default(1);
        });

        Schema::table('tb_rincian_menu_temp', function (Blueprint $table) {
            $table->unsignedBigInteger('id_satuan')->after('total_harga')->default(1);
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
            $table->dropColumn('id_satuan');
        });

        Schema::table('tb_rincian_menu_temp', function (Blueprint $table) {
            $table->dropColumn('id_satuan');
        });
    }
}
