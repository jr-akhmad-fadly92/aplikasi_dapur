<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuahMasterBahan extends Model
{
    use HasFactory;

    protected $table = 'tb_master_bahan'; // Nama tabel yang benar
    //protected $primaryKey = 'id_bahan'; // Mengasumsikan satuan_gudang adalah PK atau unique key
    public $incrementing = false; // Asumsi satuan_gudang adalah string/UUID
    protected $keyType = 'string';

    protected $fillable = [
        'bahan', // Ini yang akan menjadi 'nama buah'
        'gramasi',
        'satuan_gudang',
        'satuan_bahan',
        'jenis', // Ini bisa digunakan untuk filter 'buah'
        // Tambahkan kolom lain jika ada yang relevan
    ];

    /**
     * Relasi balik: Satu SuppMasterBahan bisa dimiliki oleh banyak SuppRincianMenuTemp.
     */
    public function rincianMenuTemps()
    {
        // Parameter 1: Model tujuan
        // Parameter 2: Foreign key di model tujuan (SuppRincianMenuTemp)
        // Parameter 3: Local key di model saat ini (SuppMasterBahan)
        return $this->hasMany(BuahRincianMenuTemp::class, 'id_resep', 'satuan_gudang');
    }
}