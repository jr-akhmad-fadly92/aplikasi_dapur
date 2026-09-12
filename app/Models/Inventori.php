<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventori extends Model
{
    use HasFactory;

    protected $table = 'tb_inventori';

    protected $fillable =
    [
        'kode_barang',
        'nama_barang',
        'jenis_barang',
        'jumlah_stok',
    ];

    public function transaksi()
    {
        return $this->hasMany(TransaksiInventori::class, 'kode_barang', 'kode_barang');
    }
}
