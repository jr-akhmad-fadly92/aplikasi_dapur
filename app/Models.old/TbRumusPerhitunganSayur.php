<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbRumusPerhitunganSayur extends Model
{
    use HasFactory;
    protected $table = 'tb_rumus_perhitungan_sayur';

    protected $fillable = [
        'id_menu',
        'sayur_porsi_a',
        'sayur_porsi_b',
        'sayur_porsi_c',
        'sayur_porsi_d',
        'kebutuhan_total_matang',
        'kebutuhan_sayur_a',
        'kebutuhan_sayur_b',
        'kebutuhan_sayur_c',
        'kebutuhan_sayur_d',
        'penyusutan_sayur_a',
        'penyusutan_sayur_b',
        'penyusutan_sayur_c',
        'penyusutan_sayur_d',
        'kebutuhan_matang_a',
        'kebutuhan_matang_b',
        'kebutuhan_matang_c',
        'kebutuhan_matang_d',
        'kebutuhan_matang_realisasi',
        'kebutuhan_total_mentah',
        'kapasitas_tilting',
        'jumlah_masak',
        'status'
    ];
}
