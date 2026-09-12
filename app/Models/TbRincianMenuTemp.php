<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbRincianMenuTemp extends Model
{
    use HasFactory;
    protected $table = 'tb_rincian_menu_temp';

    protected $fillable = [
        'id_menu',
        'id_resep',
        'id_bahan',
        'jumlah',
        'bumbu',
        'harga',
        'total_harga',
        'jumlah_box',
        'id_kontrak',
        'id_satuan',
        'keterangan'
    ];
}
