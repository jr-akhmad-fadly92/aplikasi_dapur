<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiExportIngestLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_export_ingest_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('menu_id')->unique();
            $table->uuid('export_ref')->nullable()->unique();
            $table->timestamp('exported_at')->nullable();
            $table->timestamp('pulled_at')->nullable();
            $table->timestamp('last_polled_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
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
        Schema::dropIfExists('api_export_ingest_logs');
    }
}
