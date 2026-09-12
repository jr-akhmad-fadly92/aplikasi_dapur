<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class warehouseTransaksi extends Model
{
    use HasFactory;

    protected $table = 'warehouse_transaksi';
    protected $fillable = [
        'id_penerimaan',
        'kode_wadah',
        'no_po',
        'nama_barang',
        'tanggal_masuk',
        'tanggal_keluar',
        'status',
        'jumlah',
        'id_satuan',
        'tanggal_akan_keluar',
        'referensi_masuk',
        'referensi_keluar',
        'lokasi',
        'jenis',
        'id_parent',
        'keterangan',
    ];

    public function satuan()
    {
        return $this->belongsTo('App\Models\TbSatuan', 'id_satuan', 'id');
    }
    public function tbPenerimaan()
    {
        return $this->belongsTo('App\Models\TbPenerimaan', 'id_penerimaan', 'id');
    }
}

