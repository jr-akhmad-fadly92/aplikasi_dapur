<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('table');
            $table->string('user_id');
            $table->text('ip_address');
            $table->text('platform');
            $table->text('browser');
            $table->text('activity');
            $table->text('method_function');
            $table->text('browser_version');
            $table->text('is_mobile');
            $table->text('is_robot');
            $table->text('is_desktop');
            $table->text('referer');
            $table->text('agent_string');
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
        Schema::dropIfExists('api_logs');
    }
}
