<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailKbm extends Model
{
    use HasFactory;
    protected $table = 'tb_detail_kbm';

    protected $fillable = [
        'id_kbm',
        'tanggal',
        'keterangan',
        'status',
        'minggu',
        'po',
        'pengiriman',
        'pembayaran',
    ];
}
