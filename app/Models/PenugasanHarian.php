<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenugasanHarian extends Model
{
    use HasFactory;
    // Nama tabel
    protected $table = 'tb_penugasan_harian';
    // Kolom yang bisa diisi (fillable)
    protected $fillable = [
        'id_penugasan',
        'id_karyawan',
        'id_tugas',
        'id_tugas_2',
        'id_tugas_3',
        'id_tugas_4',
        'waktu_mulai',
        'waktu_selesai',
        'tanggal',
        'tanggal_selesai',
        'id_waktu_kerja',
        'id_bagian',
    ];
}
