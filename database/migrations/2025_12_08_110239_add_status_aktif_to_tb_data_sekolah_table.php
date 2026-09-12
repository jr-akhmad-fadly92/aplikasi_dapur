<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusAktifToTbDataSekolahTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_data_sekolah', function (Blueprint $table) {
            $table->tinyInteger('status_aktif')->default(1)->after('id')->comment('1=aktif, 0=non-aktif');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_data_sekolah', function (Blueprint $table) {
            $table->dropColumn('status_aktif');
        });
    }
}
