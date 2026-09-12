<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GramasiResep extends Model
{
    use HasFactory;

    protected $table = 'tb_gramasi_resep';

    protected $fillable = [
        'id_resep',
        'gramasi_a',
        'gramasi_b',
        'status_bahan'
    ];
}
