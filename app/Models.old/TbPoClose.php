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
        'tanggl_tutup_po',
    ];
}
