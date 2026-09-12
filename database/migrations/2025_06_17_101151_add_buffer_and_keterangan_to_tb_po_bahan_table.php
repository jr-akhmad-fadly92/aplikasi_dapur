<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBufferAndKeteranganToTbPoBahanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_po_bahan', function (Blueprint $table) {
            $table->integer('buffer')->nullable()->after('jumlah_po');
            $table->string('keterangan')->nullable()->after('buffer'); // bisa diganti ->text() jika perlu panjang
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
            $table->dropColumn(['buffer', 'keterangan']);
        });
    }
}
