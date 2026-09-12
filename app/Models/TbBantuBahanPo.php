<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbBantuBahanPo extends Model
{
    use HasFactory;
    protected $table = 'tb_bantu_bahan_po';
    protected $fillable = [
        'id_menu_harian',
        'id_resep',
        'id_bahan',
        'jumlah',
        'bumbu',
        'id_po',
        'jumlah_bahan',
        'satuan',
        'jumlah_po',
        'tanggal_kedatangan',
        'tanggal_digunakan',
        'id_kontrak'
    ];
}
