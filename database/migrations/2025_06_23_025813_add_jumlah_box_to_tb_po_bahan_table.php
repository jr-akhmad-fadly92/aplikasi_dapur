<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJumlahBoxToTbPoBahanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_po_bahan', function (Blueprint $table) {
            $table->integer('jumlah_box')->nullable()->after('buffer'); // letakkan setelah kolom buffer
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
            $table->dropColumn('jumlah_box');
        });
    }
}
