<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_gizi', function (Blueprint $table) {
            $table->id();
            $table->integer('no')->unique();
            $table->string('kode', 10)->unique();
            $table->string('nama_bahan_makanan');

            // Komposisi Zat Gizi per 100 gram BDD
            $table->decimal('air_g', 8, 2)->nullable();          // Air (g)
            $table->decimal('energi_kal', 8, 2)->nullable();     // Energi (Kal)
            $table->decimal('protein_g', 8, 2)->nullable();      // Protein (g)
            $table->decimal('lemak_g', 8, 2)->nullable();        // Lemak (g)
            $table->decimal('karbohidrat_g', 8, 2)->nullable();  // Karbohidrat (g)
            $table->decimal('serat_g', 8, 2)->nullable();        // Serat (g)
            $table->decimal('abu_g', 8, 2)->nullable();          // Abu (g)

            // Mineral
            $table->decimal('kalsium_ca_mg', 10, 2)->nullable(); // Kalsium/Ca (mg)
            $table->decimal('fosfor_p_mg', 10, 2)->nullable();   // Fosfor/P (mg)
            $table->decimal('besi_fe_mg', 10, 2)->nullable();    // Besi/Fe (mg)
            $table->decimal('natrium_na_mg', 10, 2)->nullable(); // Natrium/Na (mg)
            $table->decimal('kalium_ka_mg', 10, 2)->nullable();  // Kalium/Ka (mg)
            $table->decimal('tembaga_cu_mg', 10, 4)->nullable(); // Tembaga/Cu (mg)
            $table->decimal('seng_zn_mg', 10, 2)->nullable();    // Seng/Zn (mg)

            // Vitamin
            $table->decimal('retinol_mcg', 10, 2)->nullable();          // Retinol/Vit.A (mcg)
            $table->decimal('beta_karoten_mcg', 10, 2)->nullable();     // β-Karoten (mcg)
            $table->decimal('karoten_total_mcg', 10, 2)->nullable();    // Karoten Total (mcg)
            $table->decimal('thiamin_mg', 10, 4)->nullable();           // Thiamin/Vit.B1 (mg)
            $table->decimal('riboflavin_mg', 10, 4)->nullable();        // Riboflavin/Vit.B2 (mg)
            $table->decimal('niasin_mg', 10, 2)->nullable();            // Niasin (mg)
            $table->decimal('vitamin_c_mg', 10, 2)->nullable();         // Vitamin C (mg)

            // Info tambahan
            $table->decimal('bdd_pct', 5, 2)->nullable();           // BDD (%)
            $table->string('mentah_olahan', 20)->nullable();         // Mentah / Olahan
            $table->string('kelompok_makanan', 50)->nullable();      // Kelompok Makanan
            $table->string('sumber_tkpi', 50)->nullable();           // Sumber TKPI

            $table->timestamps();

            $table->index('kelompok_makanan');
            $table->index('nama_bahan_makanan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_gizi');
    }
};
