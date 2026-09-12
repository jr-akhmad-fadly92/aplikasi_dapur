<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RumusPerhitunganSayur extends Model
{
    protected $table = 'tb_rumus_perhitungan_sayur';

    public function resep() 
    {
        return $this->belongsTo(RumusResepSayur::class, 'id_resep', 'id');
    }

     public function bahan()
    {
        return $this->belongsTo(MasterBahanSayur::class, 'id_bahan', 'id');
    }
}
