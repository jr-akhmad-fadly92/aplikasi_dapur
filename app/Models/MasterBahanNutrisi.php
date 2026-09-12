<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterBahanNutrisi extends Model
{
    use HasFactory;

    // Use legacy alias view that maps `data_gizi` columns to old app columns
    protected $table = 'tb_master_bahan_nutrisi';

    // Accept both legacy attribute names and the `data_gizi` column names
    protected $fillable = [
        // legacy names used across the app
        'no', 'kode', 'nama_bahan', 'kelompok', 'mentah_olahan', 'sumber', 'bdd',
        'air', 'energi', 'protein', 'lemak', 'karbohidrat', 'serat', 'abu', 'natrium', 'kalium', 'kalsium', 'magnesium', 'fosfor', 'besi', 'seng',
        // data_gizi column names
        'nama_bahan_makanan', 'bdd_pct', 'air_g', 'energi_kal', 'protein_g', 'lemak_g', 'karbohidrat_g', 'serat_g', 'abu_g', 'natrium_na_mg', 'kalium_ka_mg', 'kalsium_ca_mg', 'magnesium_mg', 'fosfor_p_mg', 'besi_fe_mg', 'seng_zn_mg'
    ];

    protected $casts = [
        // legacy cast names
        'bdd' => 'float',
        'air' => 'float',
        'energi' => 'float',
        'protein' => 'float',
        'lemak' => 'float',
        'karbohidrat' => 'float',
        'serat' => 'float',
        'abu' => 'float',
        'natrium' => 'float',
        'kalium' => 'float',
        'kalsium' => 'float',
        'magnesium' => 'float',
        'fosfor' => 'float',
        'besi' => 'float',
        'seng' => 'float',
        // data_gizi columns
        'bdd_pct' => 'float',
        'air_g' => 'float',
        'energi_kal' => 'float',
        'protein_g' => 'float',
        'lemak_g' => 'float',
        'karbohidrat_g' => 'float',
        'serat_g' => 'float',
        'abu_g' => 'float',
        'natrium_na_mg' => 'float',
        'kalium_ka_mg' => 'float',
        'kalsium_ca_mg' => 'float',
        'fosfor_p_mg' => 'float',
        'besi_fe_mg' => 'float',
        'seng_zn_mg' => 'float',
    ];

    // Attribute aliases to keep the rest of the app working with legacy names
    public function getNamaBahanAttribute()
    {
        return $this->attributes['nama_bahan_makanan'] ?? $this->attributes['nama_bahan'] ?? null;
    }

    public function setNamaBahanAttribute($value)
    {
        $this->attributes['nama_bahan_makanan'] = $value;
    }

    public function getBddAttribute()
    {
        return $this->attributes['bdd_pct'] ?? $this->attributes['bdd'] ?? null;
    }

    public function setBddAttribute($value)
    {
        $this->attributes['bdd_pct'] = $value;
    }

    public function getProteinAttribute()
    {
        return $this->attributes['protein_g'] ?? $this->attributes['protein'] ?? null;
    }

    public function setProteinAttribute($value)
    {
        $this->attributes['protein_g'] = $value;
    }

    public function getEnergiAttribute()
    {
        return $this->attributes['energi_kal'] ?? $this->attributes['energi'] ?? null;
    }

    public function setEnergiAttribute($value)
    {
        $this->attributes['energi_kal'] = $value;
    }

    public function getAirAttribute()
    {
        return $this->attributes['air_g'] ?? $this->attributes['air'] ?? null;
    }

    public function setAirAttribute($value)
    {
        $this->attributes['air_g'] = $value;
    }

    public function getKarbohidratAttribute()
    {
        return $this->attributes['karbohidrat_g'] ?? $this->attributes['karbohidrat'] ?? null;
    }

    public function setKarbohidratAttribute($value)
    {
        $this->attributes['karbohidrat_g'] = $value;
    }

    public function getLemakAttribute()
    {
        return $this->attributes['lemak_g'] ?? $this->attributes['lemak'] ?? null;
    }

    public function setLemakAttribute($value)
    {
        $this->attributes['lemak_g'] = $value;
    }

    public function getSeratAttribute()
    {
        return $this->attributes['serat_g'] ?? $this->attributes['serat'] ?? null;
    }

    public function setSeratAttribute($value)
    {
        $this->attributes['serat_g'] = $value;
    }

    public function getAbuAttribute()
    {
        return $this->attributes['abu_g'] ?? $this->attributes['abu'] ?? null;
    }

    public function setAbuAttribute($value)
    {
        $this->attributes['abu_g'] = $value;
    }

    // natrium/kalium/kalsium mapping
    public function getNatriumAttribute()
    {
        return $this->attributes['natrium_na_mg'] ?? $this->attributes['natrium'] ?? null;
    }

    public function setNatriumAttribute($value)
    {
        $this->attributes['natrium_na_mg'] = $value;
    }

    public function getKaliumAttribute()
    {
        return $this->attributes['kalium_ka_mg'] ?? $this->attributes['kalium'] ?? null;
    }

    public function setKaliumAttribute($value)
    {
        $this->attributes['kalium_ka_mg'] = $value;
    }

    public function getKalsiumAttribute()
    {
        return $this->attributes['kalsium_ca_mg'] ?? $this->attributes['kalsium'] ?? null;
    }

    public function setKalsiumAttribute($value)
    {
        $this->attributes['kalsium_ca_mg'] = $value;
    }

    public function realisasiAkg()
    {
        return $this->hasMany(ResepRealisasiAkg::class, 'id_master_bahan_nutrisi');
    }
}
