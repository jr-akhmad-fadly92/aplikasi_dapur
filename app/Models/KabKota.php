<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KabKota extends Model
{
    use HasFactory;

    protected $table = 'tb_kabkota';

    protected $fillable = [
        'provinsi_id',
        'kabupaten_kota',
        'ibukota',
        'k_bsni',
    ];
}
