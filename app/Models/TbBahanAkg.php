<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbBahanAkg extends Model
{
    use HasFactory;

    protected $table = 'tb_bahan_akg';

    public $timestamps = false;

    protected $fillable = [
        'id_bahan',
        'id_master_bahan_nutrisi',
        'bdd',
        'energi',
        'protein',
        'lemak',
        'karbohidrat',
        'serat',
        'natrium',
    ];

    protected $casts = [
        'bdd' => 'decimal:2',
        'energi' => 'decimal:2',
        'protein' => 'decimal:2',
        'lemak' => 'decimal:2',
        'karbohidrat' => 'decimal:2',
        'serat' => 'decimal:2',
        'natrium' => 'decimal:2',
    ];

    public function bahan()
    {
        return $this->belongsTo(TbMasterBahan::class, 'id_bahan');
    }

    public function masterBahanNutrisi()
    {
        return $this->belongsTo(MasterBahanNutrisi::class, 'id_master_bahan_nutrisi');
    }
}
