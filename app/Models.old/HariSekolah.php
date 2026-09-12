<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HariSekolah extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_tb_data_sekolah',
        'senin',
        'selasa',
        'rabu',
        'kamis',
        'jumat',
        'sabtu',
        'minggu',
        'status',
    ];

    protected $casts = [
        'senin'  => 'boolean',
        'selasa' => 'boolean',
        'rabu'   => 'boolean',
        'kamis'  => 'boolean',
        'jumat'  => 'boolean',
        'sabtu'  => 'boolean',
        'minggu' => 'boolean',
        'status' => 'boolean',
    ];
}
