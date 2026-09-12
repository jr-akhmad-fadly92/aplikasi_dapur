<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanBiayaOprasional extends Model
{
    protected $table = 'tb_kas_kecil_transaksi';
     protected $fillable = [
        'tanggal',
        'jenis_transaksi',
        'master_bahan_id',
        'deskripsi',
        'jumlah',
        'nama_karyawan',
        'nomor_transaksi',
        'status',
        'id_parent',
    ];
 
}