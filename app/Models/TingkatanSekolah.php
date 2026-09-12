<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TingkatanSekolah extends Model
{
    use HasFactory;

    protected $table = 'tb_tingkatan_sekolah';

    protected $fillable = ['id',
        'jenjang_sekolah',
        'golongan',
        'gramasi_nasi',
        'gramasi_sayur',
        'gramasi_protein',
        'gramasi_buah',
        'gramasi_susu',
    ];
}
