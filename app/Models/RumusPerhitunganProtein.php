<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RumusPerhitunganProtein extends Model
{
    protected $table = 'tb_rumus_perhitungan_protein';

    public function rincianMenuProtein()
    {
        return $this->hasMany(RincianMenuProtein::class, 'id_menu', 'id_menu');
    }
}
