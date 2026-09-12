<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KbmSekolah extends Model
{
    use HasFactory;
    protected $table = 'tb_kbm_sekolah';

    protected $fillable = [
        'tahun_ajaran',
        'semester',
        'tanggal_mulai_kbm',
        'tanggal_selesai_kbm'
    ];
}
