<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiInventori extends Model
{
    use HasFactory;

    protected $table = 'tb_transaksi_inventori';

    protected $fillable =
    [
        'kode_barang',
        'kondisi',
        'pic',
        'jumlah',
        'status',
    ];

    public function inventori()
    {
        return $this->belongsTo(Inventori::class, 'kode_barang', 'kode_barang');
    }
}
