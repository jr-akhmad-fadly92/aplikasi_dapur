<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoxBahanBaku extends Model
{
    use HasFactory;
    protected $table = 'tb_box_bahan_baku';

    protected $fillable = [
        'id_bahan',
        'isi_per_box',
        'hasil_matang',
        'penyusutan'
    ];
}
