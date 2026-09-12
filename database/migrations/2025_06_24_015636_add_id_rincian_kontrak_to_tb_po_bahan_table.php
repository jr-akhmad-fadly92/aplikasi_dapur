<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdRincianKontrakToTbPoBahanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_po_bahan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_rincian_kontrak')->nullable()->after('keterangan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_po_bahan', function (Blueprint $table) {
            $table->dropColumn('id_rincian_kontrak');
        });
    }
}
