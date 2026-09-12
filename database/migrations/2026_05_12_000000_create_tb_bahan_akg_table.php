<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_bahan_akg', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_bahan');
            $table->unsignedBigInteger('id_master_bahan_nutrisi');
            $table->decimal('bdd', 5, 2)->nullable();
            $table->decimal('energi', 12, 4)->default(0);
            $table->decimal('protein', 12, 4)->default(0);
            $table->decimal('lemak', 12, 4)->default(0);
            $table->decimal('karbohidrat', 12, 4)->default(0);
            $table->decimal('serat', 12, 4)->default(0);
            $table->decimal('natrium', 12, 4)->default(0);

            $table->index('id_bahan');
            $table->index('id_master_bahan_nutrisi');
            $table->unique(['id_bahan', 'id_master_bahan_nutrisi'], 'tb_bahan_akg_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_bahan_akg');
    }
};
