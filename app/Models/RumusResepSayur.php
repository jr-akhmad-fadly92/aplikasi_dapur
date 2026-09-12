<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RumusResepSayur extends Model
{
    protected $table = 'tb_resep';

    public function bahan()
    {
        return $this->hasMany(MasterBahanSayur::class, 'id_resep', 'id'); // asumsi id_resep ada di master bahan
    }
}
