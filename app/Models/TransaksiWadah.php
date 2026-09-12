<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiWadah extends Model
{
    use HasFactory;
    protected $table = 'tb_transaksi_wadah';
    protected $fillable = [
        'id_penerimaan',
        'qr_code_wadah',
        'jumlah_berat_sebelum',
        'tanggal_dan_waktu_masuk',
        'jumlah_berat_sesudah',
        'tanggal_dan_waktu_sesudah',
        'tanggal_dan_waktu_keluar_gudang',
        'tanggal_dan_waktu_harus_keluar',
        'status',
        'lokasi',
        'created_at'
    ];
}
