<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasKecilTransaksi extends Model
{
    use HasFactory;
    protected $table = 'tb_kas_kecil_transaksi';

    protected $fillable = [
        'tanggal',
        'jenis_transaksi',
        'master_bahan_id',
        'nama_karyawan',
        'nomor_transaksi',
        'status',
        'id_parent',
        'deskripsi',
        'jumlah',
        'nomor_po'
    ];
}
