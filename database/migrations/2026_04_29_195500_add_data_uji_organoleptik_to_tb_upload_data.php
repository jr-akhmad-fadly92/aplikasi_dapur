<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('tb_upload_data')) {
            Schema::table('tb_upload_data', function (Blueprint $table) {
                if (!Schema::hasColumn('tb_upload_data', 'data_uji_organoleptik')) {
                    $table->string('data_uji_organoleptik')->nullable()->after('data_penerimaan_pangan');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('tb_upload_data')) {
            Schema::table('tb_upload_data', function (Blueprint $table) {
                if (Schema::hasColumn('tb_upload_data', 'data_uji_organoleptik')) {
                    $table->dropColumn('data_uji_organoleptik');
                }
            });
        }
    }
};
