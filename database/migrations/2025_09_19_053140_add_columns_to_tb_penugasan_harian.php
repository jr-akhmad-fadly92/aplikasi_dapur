<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddColumnsToTbPenugasanHarian extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_penugasan_harian', function (Blueprint $table) {
            // ubah tipe data id_penugasan jadi varchar(30)

            DB::statement('ALTER TABLE tb_penugasan_harian MODIFY id_penugasan VARCHAR(30)');
            DB::statement('ALTER TABLE tb_penugasan_harian MODIFY id_tugas BIGINT NULL');
            DB::statement('ALTER TABLE tb_penugasan_harian MODIFY waktu_mulai TIME NULL');
            DB::statement('ALTER TABLE tb_penugasan_harian MODIFY waktu_selesai TIME NULL');
            DB::statement('ALTER TABLE tb_penugasan_harian MODIFY tanggal DATE NULL');

            // tambah kolom baru
            $table->unsignedBigInteger('id_tugas_2')->nullable()->after('id_tugas');
            $table->unsignedBigInteger('id_tugas_3')->nullable()->after('id_tugas_2');
            $table->unsignedBigInteger('id_tugas_4')->nullable()->after('id_tugas_3');
            $table->date('tanggal_selesai')->nullable()->after('tanggal');
            $table->unsignedBigInteger('id_waktu_kerja')->nullable()->after('tanggal_selesai');
            $table->unsignedBigInteger('id_bagian')->nullable()->after('id_waktu_kerja');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // rollback perubahan
        DB::statement('ALTER TABLE tb_penugasan_harian MODIFY id_penugasan BIGINT ');
        DB::statement('ALTER TABLE tb_penugasan_harian MODIFY id_tugas BIGINT NOT NULL');

        DB::statement('ALTER TABLE tb_penugasan_harian MODIFY waktu_mulai TIME NOT NULL');
        DB::statement('ALTER TABLE tb_penugasan_harian MODIFY waktu_selesai TIME NOT NULL');
        DB::statement('ALTER TABLE tb_penugasan_harian MODIFY tanggal DATE NOT NULL');

        Schema::table('tb_penugasan_harian', function (Blueprint $table) {
            $table->dropColumn([
                'id_tugas_2',
                'id_tugas_3',
                'id_tugas_4',
                'tanggal_selesai',
                'id_waktu_kerja',
                'id_bagian',
            ]);
        });
    }
}
