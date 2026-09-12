<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataSekolah extends Model
{
    use HasFactory;

    protected $table = 'tb_data_sekolah';

    protected $fillable = [
        'id',
        'nama_sekolah',
        'jenjang_sekolah',
        'jumlah_siswa',
        'alamat_sekolah',
        'lintang',
        'bujur',
        'id_dapur',
        'jumlah_a',
        'jumlah_b',
        'kode_prop',
        'propinsi',
        'kode_kab_kota',
        'kabupaten_kota',
        'kode_kec',
        'kecamatan',
        'npsn',
        'status',
        'bentuk',
        'jarak',
        'kelurahan',
        'email',
        'kepala_sekolah',
        'no_telp',
        'waktu_tempuh',

    ];




    protected $casts = [
        'hari_sekolah' => 'array', // Mengubah JSON menjadi array saat diakses

    ];

    public function tb_data_dapur()
    {
        return $this->belongsTo(DataDapur::class, 'nama_dapur', 'id');
    }

    public function sekolahAktif()
    {
        return $this->hasMany(SekolahAktif::class, 'id_tb_data_sekolah', 'id');
    }

    public function rincian_sekolah()
    {
        return $this->hasMany(rincian_sekolah::class, 'id_sekolah', 'id');
    }

    public function siswaSekolah()
    {
        return $this->hasMany(SiswaSekolah::class, 'npsn', 'npsn');
    }
}
