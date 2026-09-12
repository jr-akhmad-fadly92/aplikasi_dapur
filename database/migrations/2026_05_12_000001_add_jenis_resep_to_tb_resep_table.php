<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_resep', function (Blueprint $table) {
            $table->enum('jenis_resep', ['tidak diolah', 'goreng', 'rebus/kukus', 'tumis', 'potong', 'utuh', 'lain-lain'])
                  ->default('tidak diolah')
                  ->after('nama_resep');
        });
    }

    public function down(): void
    {
        Schema::table('tb_resep', function (Blueprint $table) {
            $table->dropColumn('jenis_resep');
        });
    }
};
