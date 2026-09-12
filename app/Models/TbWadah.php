<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbWadah extends Model
{
    use HasFactory;
    // Tentukan nama tabel di database
    protected $table = 'tb_wadah';

    // Tentukan kolom yang dapat diisi (mass assignment)
    protected $fillable = [
        'qr_code',
        'status',
    ];
}
