<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTbWaktuKerjaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_waktu_kerja', function (Blueprint $table) {
            $table->id(); // auto increment
            $table->time('jam_kerja');
            $table->timestamps();
        });

        // isi default
        DB::table('tb_waktu_kerja')->insert([
            ['jam_kerja' => '04:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['jam_kerja' => '05:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['jam_kerja' => '06:30:00', 'created_at' => now(), 'updated_at' => now()],
            ['jam_kerja' => '07:30:00', 'created_at' => now(), 'updated_at' => now()],
            ['jam_kerja' => '13:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['jam_kerja' => '21:00:00', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_waktu_kerja');
    }
}
