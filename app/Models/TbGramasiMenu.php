<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbGramasiMenu extends Model
{
    use HasFactory;
    protected $table = 'tb_gramasi_menu';

    protected $fillable = [
        'id_menu',
        'gramasi_karbo_a',
        'porsi_tray_a',
        'kg_tray_a',
        'gramasi_lauk_a',
        'gramasi_sayur_utama_a',
        'gramasi_sayur_kedua_a',
        'gramasi_buah_a',
        'gramasi_suplemen_a',
        'gramasi_karbo_b',
        'porsi_tray_b',
        'kg_tray_b',
        'gramasi_lauk_b',
        'gramasi_sayur_utama_b',
        'gramasi_sayur_kedua_b',
        'gramasi_buah_b',
        'gramasi_suplemen_b',
    ];
}
