<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaktuKerja extends Model
{
    use HasFactory;
    protected $table = 'tb_waktu_kerja';

    protected $fillable = ['jam_kerja'];
}
