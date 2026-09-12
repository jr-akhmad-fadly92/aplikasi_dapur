<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPeriodeJumlahhariToTbLaporanRealisasiAnggaran extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_laporan_realisasi_anggaran', function (Blueprint $table) {
            // Add new columns if they don't exist yet
            if (!Schema::hasColumn('tb_laporan_realisasi_anggaran', 'periode')) {
                $table->string('periode')->nullable();
            }

            if (!Schema::hasColumn('tb_laporan_realisasi_anggaran', 'jumlah_hari')) {
                $table->integer('jumlah_hari')->default(0);
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
        Schema::table('tb_laporan_realisasi_anggaran', function (Blueprint $table) {
            if (Schema::hasColumn('tb_laporan_realisasi_anggaran', 'periode')) {
                $table->dropColumn('periode');
            }

            if (Schema::hasColumn('tb_laporan_realisasi_anggaran', 'jumlah_hari')) {
                $table->dropColumn('jumlah_hari');
            }
        });
    }
}
