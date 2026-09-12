<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeteranganToRincianMenuTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rincian_menu_harian', function (Blueprint $table) {
            $table->string('keterangan', 255)->nullable()->default('-')->after('updated_at');
        });

        Schema::table('tb_rincian_menu_temp', function (Blueprint $table) {
            $table->string('keterangan', 255)->nullable()->default('-')->after('updated_at');
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
            $table->dropColumn('keterangan');
        });

        Schema::table('tb_rincian_menu_temp', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
}
