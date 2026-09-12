<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHargaColumnsToRincianMenuHarianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rincian_menu_harian', function (Blueprint $table) {
            $table->integer('harga')->default(0)->after('bumbu');
            $table->integer('total_harga')->default(0)->after('harga');
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
            $table->dropColumn(['harga', 'total_harga']);
        });
    }
}
