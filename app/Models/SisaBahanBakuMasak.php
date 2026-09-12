<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SisaBahanBakuMasak extends Model
{
    use HasFactory;

    protected $table = 'tb_sisa_bahan_baku_masak';

    protected $fillable = [
        'tanggal',
        'id_menu',
        'sisa_karbo',
        'sisa_protein',
        'sisa_sayur',
        'sisa_buah',
        'sisa_susu',
        'keterangan',
    ];
}
