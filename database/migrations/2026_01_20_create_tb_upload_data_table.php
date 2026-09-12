<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_upload_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_menu')->nullable();
            $table->date('tanggal_pelayanan');
            $table->string('data_menu')->nullable()->comment('menu_tanggal.pdf');
            $table->string('data_po')->nullable()->comment('po_tanggal.pdf');
            $table->string('data_sj_kp')->nullable()->comment('sj_kp_tanggal.pdf');
            $table->string('data_invoice')->nullable()->comment('invoice_tanggal.pdf');
            $table->string('data_penerimaan_pangan')->nullable()->comment('penerimaan_pangan_tanggal.pdf');
            $table->string('data_penerimaan_non_pangan')->nullable()->comment('penerimaan_non_pangan_tanggal.pdf');
            $table->string('data_gudang')->nullable()->comment('gudang_tanggal.pdf');
            $table->string('data_hasil_masak')->nullable()->comment('hasil_masak_tanggal.pdf');
            $table->string('data_sj_sekolah')->nullable()->comment('sj_sekolah_tanggal.pdf');
            $table->string('data_counter_ompreng')->nullable()->comment('counter_tanggal.pdf');
            $table->timestamps();
            
            $table->foreign('id_menu')->references('id')->on('tb_menu')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_upload_data');
    }
};
