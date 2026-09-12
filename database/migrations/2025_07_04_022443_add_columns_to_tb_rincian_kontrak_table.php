<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToTbRincianKontrakTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_rincian_kontrak', function (Blueprint $table) {
            $table->string('merek_bahan')->nullable()->after('satuan_bahan');
            $table->string('status')->nullable()->after('merek_bahan');
            $table->string('kemasan')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_rincian_kontrak', function (Blueprint $table) {
            $table->dropColumn(['merek_bahan', 'status', 'kemasan']);
        });
    }
}
