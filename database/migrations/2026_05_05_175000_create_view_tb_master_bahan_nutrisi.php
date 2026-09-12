<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Create a view that maps `data_gizi` columns to the legacy `tb_master_bahan_nutrisi` column names
        DB::statement(<<<'SQL'
            CREATE OR REPLACE VIEW `tb_master_bahan_nutrisi` AS
            SELECT
                id,
                `no`,
                `kode`,
                `nama_bahan_makanan` AS `nama_bahan`,
                `kelompok_makanan` AS `kelompok`,
                `mentah_olahan`,
                `sumber_tkpi` AS `sumber`,
                `bdd_pct` AS `bdd`,
                `air_g` AS `air`,
                `energi_kal` AS `energi`,
                `protein_g` AS `protein`,
                `lemak_g` AS `lemak`,
                `karbohidrat_g` AS `karbohidrat`,
                `serat_g` AS `serat`,
                `abu_g` AS `abu`,
                `natrium_na_mg` AS `natrium`,
                `kalium_ka_mg` AS `kalium`,
                `kalsium_ca_mg` AS `kalsium`,
                NULL AS `magnesium`,
                `fosfor_p_mg` AS `fosfor`,
                `besi_fe_mg` AS `besi`,
                `seng_zn_mg` AS `seng`,
                created_at,
                updated_at
            FROM `data_gizi`;
        SQL
        );
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS `tb_master_bahan_nutrisi`');
    }
};
