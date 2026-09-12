<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbHargaHet extends Model
{
    use HasFactory;

    protected $table = 'tb_harga_het';

    protected $fillable = [
        'id_bahan',
        'tanggal_update',
        'harga_het',
    ];
}
