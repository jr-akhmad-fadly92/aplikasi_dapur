<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbKaryawanDapurTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_karyawan_dapur', function (Blueprint $table) {
            $table->id(); // primary key auto increment
            $table->string('nik')->unique();
            $table->string('nama_karyawan');
            $table->text('alamat')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('status_ktp', 20)->nullable();
            $table->enum('status_karyawan', ['aktif', 'nonaktif'])->default('aktif');
            $table->date('masuk_kerja');
            $table->date('keluar_kerja')->nullable();
            $table->string('id_karyawan')->unique();
            $table->string('no_bagian')->nullable(); // kalau pakai kode khusus
            $table->timestamps(); // otomatis bikin created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_karyawan_dapur');
    }
}
