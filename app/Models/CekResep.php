<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CekResep extends Model
{
    protected $table = 'tb_resep';
    protected $fillable = ['nama_resep', 'id_komponen_sehat', 'porsi_resep', 'margin'];

    public function realisasiAkg()
    {
        return $this->hasMany(ResepRealisasiAkg::class, 'id_resep');
    }
}
