<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbPenerimaan extends Model
{
    use HasFactory;
    protected $table = 'tb_penerimaan'; // Nama tabel

    protected $fillable = [
        'id_barang_po',
        'jumlah_datang',
        'jumlah_berat',
        'satuan_berat',
        'status',
        'keterangan',
        'qr_code_wadah',
        'nama_penerima'
    ];

    public function tbPoBahan()
    {
        return $this->belongsTo('App\Models\TbPoBahan', 'id_barang_po', 'id');
    }
    public function warehouseTransaksi()
    {
        return $this->hasMany('App\Models\warehouseTransaksi', 'id_penerimaan', 'id');
    }
}
