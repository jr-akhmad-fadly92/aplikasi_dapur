<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJamPelayananToTbMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_menu', function (Blueprint $table) {
            // Add new datetime column for service time (jam_pelayanan)
            if (!Schema::hasColumn('tb_menu', 'jam_pelayanan')) {
                $table->dateTime('jam_pelayanan')->nullable()->after('hari_kirim');
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
            if (Schema::hasColumn('tb_menu', 'jam_pelayanan')) {
                $table->dropColumn('jam_pelayanan');
            }
        });
    }
}
