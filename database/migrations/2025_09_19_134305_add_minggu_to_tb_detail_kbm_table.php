<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMingguToTbDetailKbmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_detail_kbm', function (Blueprint $table) {
            $table->integer('minggu')->nullable()->after('status');
            $table->integer('po')->nullable()->after('minggu');
            $table->integer('pengiriman')->nullable()->after('po');
            $table->integer('pembayaran')->nullable()->after('pengiriman');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_detail_kbm', function (Blueprint $table) {
            $table->dropColumn('minggu');
            $table->dropColumn('po');
            $table->dropColumn('pengiriman');
            $table->dropColumn('pembayaran');
        });
    }
}
