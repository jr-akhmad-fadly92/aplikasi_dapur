<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResepRealisasiAkg extends Model
{
    use HasFactory;

    protected $table = 'tb_resep_realisasi_akg';

    protected $fillable = [
        'id_resep',
        'id_master_bahan_nutrisi',
        'jumlah_gram',
        'bdd_pct',
        'energi_kcal',
        'protein_g',
        'lemak_g',
        'karbohidrat_g',
        'serat_g',
        'natrium_mg',
    ];

    protected $casts = [
        'jumlah_gram' => 'decimal:2',
        'bdd_pct' => 'decimal:2',
        'energi_kcal' => 'decimal:4',
        'protein_g' => 'decimal:4',
        'lemak_g' => 'decimal:4',
        'karbohidrat_g' => 'decimal:4',
        'serat_g' => 'decimal:4',
        'natrium_mg' => 'decimal:4',
    ];

    public function resep()
    {
        return $this->belongsTo(Resep::class, 'id_resep');
    }

    public function masterBahanNutrisi()
    {
        return $this->belongsTo(MasterBahanNutrisi::class, 'id_master_bahan_nutrisi');
    }
}