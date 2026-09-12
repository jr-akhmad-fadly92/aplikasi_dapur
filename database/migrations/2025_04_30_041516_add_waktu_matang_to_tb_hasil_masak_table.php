<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWaktuMatangToTbHasilMasakTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_hasil_masak', function (Blueprint $table) {
            //
            $table->timestamp('waktu_matang')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_hasil_masak', function (Blueprint $table) {
            //
            $table->dropColumn('waktu_matang');
        });
    }
}
