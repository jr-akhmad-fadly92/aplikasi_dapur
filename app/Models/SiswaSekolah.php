<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiswaSekolah extends Model
{
    use HasFactory;

    protected $table = 'siswa_sekolah';

    protected $fillable = [
        'npsn',
        'nisn',
        'nama',
        'kelas',
        'jenis_kelamin',
        'nama_orangtua',
        'keterangan',
        'berat_badan',
        'tinggi_badan',
        'tanggal_lahir',
        'tempat_lahir',
        'riwayat_penyakit_bawaan',
        'riwayat_penyakit_menular',
        'alergi',
        'nomor_telp_emergency',
        'golongan_darah',
        'golongan_penerimaan',
        'alamat',
        'pekerjaan_orang_tua',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke model DataSekolah berdasarkan NPSN
     */
    public function sekolah()
    {
        return $this->belongsTo(DataSekolah::class, 'npsn', 'npsn');
    }

    /**
     * Scope untuk filter berdasarkan NPSN
     */
    public function scopeByNpsn($query, $npsn)
    {
        return $query->where('npsn', $npsn);
    }

    /**
     * Scope untuk filter berdasarkan nama
     */
    public function scopeByNama($query, $nama)
    {
        return $query->where('nama', 'LIKE', '%' . $nama . '%');
    }

    /**
     * Scope untuk filter berdasarkan NISN
     */
    public function scopeByNisn($query, $nisn)
    {
        return $query->where('nisn', 'LIKE', '%' . $nisn . '%');
    }
}
