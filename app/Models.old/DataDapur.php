<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataDapur extends Model
{
    use HasFactory;

    protected $table = 'tb_data_dapur';

    protected $fillable = [
        'nama_dapur',
        'alamat_dapur',
        'nomor_dapur',
        'kota',
        'provinsi',
        'kelurahan',
        'kecamatan',
        'no_telp',
        'email',
        'pemilik',
        'kepala_dapur',
        'admin_dapur',
        'ahli_gizi',
        'ahli_akuntan',
    ];

    public function tb_kabkota()
    {
        return $this->belongsTo(KabKota::class, 'kota', 'id');
    }

    public function tb_provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'provinsi', 'id');
    }
}
