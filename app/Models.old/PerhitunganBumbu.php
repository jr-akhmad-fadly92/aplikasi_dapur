<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerhitunganBumbu extends Model
{
    use HasFactory;

    protected $table = 'tb_perhitungan_bumbu';

    protected $fillable = [
        'id',
        'id_menu_bahan',
        'pembagi',
        'pengali',
        'keterangan',
    ];
}
