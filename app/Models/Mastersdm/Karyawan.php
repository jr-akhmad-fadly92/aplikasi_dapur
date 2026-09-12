<?php

namespace App\Models\Mastersdm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'tb_karyawan';

    protected $fillable = [
        'nik',
        'nama_karyawan',
        'alamat',
        'no_hp',
        'status_karyawan',
        'id_karyawan',
        'masuk_kerja',
        'keluar_kerja',
        'no_bagian',
    ];
}