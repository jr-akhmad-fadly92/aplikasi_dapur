<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buffer extends Model
{
    use HasFactory;
    protected $table = 'tb_buffer';

    protected $fillable = [
        'buffer_menu',
        'buffer_po',
    ];
}
