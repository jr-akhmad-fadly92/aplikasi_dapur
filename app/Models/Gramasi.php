<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gramasi extends Model
{
    use HasFactory;
    protected $table = 'tb_gramasi';
    protected $fillable = ['kode', 'karbohidrat', 'protein', 'sayur', 'buah', 'susu'];
}
