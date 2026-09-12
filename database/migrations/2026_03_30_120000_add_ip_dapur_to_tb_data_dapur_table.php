<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIpDapurToTbDataDapurTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_data_dapur', function (Blueprint $table) {
            $table->string('ip_dapur')->nullable()->after('nomor_dapur');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_data_dapur', function (Blueprint $table) {
            $table->dropColumn('ip_dapur');
        });
    }
}
