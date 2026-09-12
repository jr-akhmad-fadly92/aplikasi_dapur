<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbSatuan extends Model
{
    use HasFactory;
    protected $table = 'tb_satuan';
    protected $fillable = [
        'satuan'
    ];
}
