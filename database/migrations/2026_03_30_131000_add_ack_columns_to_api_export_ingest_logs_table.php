<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAckColumnsToApiExportIngestLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_export_ingest_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('api_export_ingest_logs', 'export_ref')) {
                $table->uuid('export_ref')->nullable()->unique()->after('menu_id');
            }

            if (!Schema::hasColumn('api_export_ingest_logs', 'pulled_at')) {
                $table->timestamp('pulled_at')->nullable()->after('exported_at');
            }

            if (!Schema::hasColumn('api_export_ingest_logs', 'last_polled_at')) {
                $table->timestamp('last_polled_at')->nullable()->after('pulled_at');
            }

            if (!Schema::hasColumn('api_export_ingest_logs', 'acknowledged_at')) {
                $table->timestamp('acknowledged_at')->nullable()->after('last_polled_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_export_ingest_logs', function (Blueprint $table) {
            if (Schema::hasColumn('api_export_ingest_logs', 'acknowledged_at')) {
                $table->dropColumn('acknowledged_at');
            }

            if (Schema::hasColumn('api_export_ingest_logs', 'last_polled_at')) {
                $table->dropColumn('last_polled_at');
            }

            if (Schema::hasColumn('api_export_ingest_logs', 'pulled_at')) {
                $table->dropColumn('pulled_at');
            }

            if (Schema::hasColumn('api_export_ingest_logs', 'export_ref')) {
                $table->dropUnique('api_export_ingest_logs_export_ref_unique');
                $table->dropColumn('export_ref');
            }
        });
    }
}
