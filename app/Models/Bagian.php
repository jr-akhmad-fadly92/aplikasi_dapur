<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bagian extends Model
{
    use HasFactory;
    protected $table = 'tb_bagian';

    protected $fillable = [
        'id_bagian',
        'nama_bagian',
    ];
}
