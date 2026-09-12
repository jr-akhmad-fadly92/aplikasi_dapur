<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToTbBoxBahanBakuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_box_bahan_baku', function (Blueprint $table) {
            $table->integer('status')->default(0)->after('penyusutan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_box_bahan_baku', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
