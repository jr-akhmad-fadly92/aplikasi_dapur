<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_resep_realisasi_akg', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_resep');
            $table->unsignedBigInteger('id_master_bahan_nutrisi');
            $table->decimal('jumlah_gram', 10, 2)->default(0);
            $table->decimal('bdd_pct', 10, 2)->default(0);
            $table->decimal('energi_kcal', 12, 4)->default(0);
            $table->decimal('protein_g', 12, 4)->default(0);
            $table->decimal('lemak_g', 12, 4)->default(0);
            $table->decimal('karbohidrat_g', 12, 4)->default(0);
            $table->decimal('serat_g', 12, 4)->default(0);
            $table->decimal('natrium_mg', 12, 4)->default(0);
            $table->timestamps();

            $table->unique(['id_resep', 'id_master_bahan_nutrisi'], 'tb_resep_realisasi_akg_unique');
            $table->index('id_resep');
            $table->index('id_master_bahan_nutrisi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_resep_realisasi_akg');
    }
};