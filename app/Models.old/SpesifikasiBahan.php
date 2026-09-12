<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpesifikasiBahan extends Model
{
    use HasFactory;
    protected $table = 'tb_spesifikasi_bahan';

    protected $fillable = [
        'id',
        'id_bahan',
        'spesifikasi',
       
    ];
}
