<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWaktuMulaiMasakToTbHistoriMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_histori_menu', function (Blueprint $table) {
            //
            $table->timestamp('waktu_mulai_masak')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_histori_menu', function (Blueprint $table) {
            //
            $table->dropColumn('waktu_mulai_masak');
        });
    }
}
