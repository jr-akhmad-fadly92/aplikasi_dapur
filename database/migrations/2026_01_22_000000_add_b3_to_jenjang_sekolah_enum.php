<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddB3ToJenjangSekolahEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // For MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tb_data_sekolah MODIFY jenjang_sekolah ENUM('KB/Sederajat', 'TK/Sederajat', 'SD/Sederajat', 'SMP/Sederajat', 'SMA/Sederajat', 'SMK/Sederajat', 'Taruna', 'B3')");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // For MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tb_data_sekolah MODIFY jenjang_sekolah ENUM('KB/Sederajat', 'TK/Sederajat', 'SD/Sederajat', 'SMP/Sederajat', 'SMA/Sederajat', 'SMK/Sederajat', 'Taruna')");
        }
    }
}
