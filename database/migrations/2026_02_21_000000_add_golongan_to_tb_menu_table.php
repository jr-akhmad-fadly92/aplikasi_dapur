<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGolonganToTbMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_menu', function (Blueprint $table) {
            // Add golongan column after jam_pelayanan with default 'umum'
            if (!Schema::hasColumn('tb_menu', 'golongan')) {
                $table->enum('golongan', ['umum', 'pax_a', 'pax_b', 'busui_bumil', 'balita', 'baduta'])
                      ->default('umum')
                      ->after('jam_pelayanan');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_menu', function (Blueprint $table) {
            if (Schema::hasColumn('tb_menu', 'golongan')) {
                $table->dropColumn('golongan');
            }
        });
    }
}
