<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbMasterBantuanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_master_bantuan', function (Blueprint $table) {
            $table->id();
            $table->integer('bantuan_pangan_A')->default(0);
            $table->integer('bantuan_pangan_B')->default(0);
            $table->integer('bantuan_operasional')->default(0);
            $table->integer('bantuan_infra')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_master_bantuan');
    }
}
