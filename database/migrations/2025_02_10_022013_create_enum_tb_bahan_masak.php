<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnumTbBahanMasak extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_bahan_masak', function (Blueprint $table) {
            // Hapus kolom lama agar bisa diganti dengan ENUM
            $table->dropColumn('kategori_bahan');
            $table->dropColumn('status_bahan');
        });

        Schema::table('tb_bahan_masak', function (Blueprint $table) {
            // Tambahkan kembali kolom dengan tipe ENUM
            $table->enum('kategori_bahan', ['Matang', 'Mentah'])->after('id_satuan');
            $table->enum('status_bahan', ['Aman', 'Sedikit', 'Habis'])->after('kategori_bahan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_bahan_masak', function (Blueprint $table) {
            $table->dropColumn('kategori_bahan');
            $table->dropColumn('status_bahan');
        });

        Schema::table('tb_bahan_masak', function (Blueprint $table) {
            $table->string('kategori_bahan', 10)->after('id_satuan');
            $table->string('status_bahan', 10)->after('kategori_bahan');
        });
    }
}
