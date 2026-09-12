<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaraMasak extends Model
{
    use HasFactory;

    protected $table = 'tb_cara_masak';

    protected $fillable = [
        'resep',
        'cara_masak',
    ];

    // In CaraMasak model
    
}
