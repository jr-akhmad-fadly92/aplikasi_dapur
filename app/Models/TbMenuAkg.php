<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbMenuAkg extends Model
{
    use HasFactory;
    protected $table = 'tb_menu_akg';

    protected $fillable = [
        'id_menu',
        'komponen_sehat',
        'id_nutrisi',
        'energi',
        'protein',
        'lemak',
        'karbo',
        'serat',
        'natrium',
    ];
}
