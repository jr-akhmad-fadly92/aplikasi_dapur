<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNomorPoToTbKasKecilTransaksiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_kas_kecil_transaksi', function (Blueprint $table) {
            $table->string('nomor_po', 100)->nullable()->after('id_parent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_kas_kecil_transaksi', function (Blueprint $table) {
            $table->dropColumn('nomor_po');
        });
    }
}
