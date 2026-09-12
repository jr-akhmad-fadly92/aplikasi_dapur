<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbPoClose extends Model
{
    use HasFactory;
    protected $table = 'tb_po_close';

    protected $fillable = [
        'id_po',
        'tanggal_tutup_po',
        'jenis_po'
    ];
}
