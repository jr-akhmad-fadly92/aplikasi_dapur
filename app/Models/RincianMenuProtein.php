<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RincianMenuProtein extends Model
{
    protected $table = 'tb_rincian_menu_temp';

    public function resep()
    {
        return $this->belongsTo(RumusResep::class, 'id_resep',);
    }
}
