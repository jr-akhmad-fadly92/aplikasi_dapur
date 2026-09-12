<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tb_karyawan extends Model
{
    use HasFactory;
    protected $table = 'tb_karyawan';

    // Tentukan kolom-kolom yang dapat diisi (fillable)
    protected $fillable = [
        'id',
        'id_karyawan',
        'nama',
        'jabatan',
        'tanggal_masuk',
        'status',
        'kontak',
    ];
    
}
