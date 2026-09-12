<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanRealisasiAnggaran extends Model
{
    use HasFactory;
    protected $table = 'tb_laporan_realisasi_anggaran';

    protected $fillable = [
        'id_laporan',
        'bgn',
        'yayasan',
        'pihak_lain',
        'periode_awal',
        'periode_akhir',
        'periode',
        'jumlah_hari',
    ];
}
