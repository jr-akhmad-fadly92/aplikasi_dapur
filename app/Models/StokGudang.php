<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TbMasterBahan;
use App\Models\TbSatuan;

class StokGudang extends Model
{
    use HasFactory;
    protected $table = 'tb_stok_gudang';

    protected $fillable = [
        'nama_barang',
        'jumlah',
        'id_satuan',
        'kategori_bahan',
        'status_bahan',
    ];

    public function tb_master_bahan()
    {
        return $this->belongsTo(TbMasterBahan::class, 'nama_barang');
    }

    public function tb_satuan()
    {
        return $this->belongsTo(TbSatuan::class, 'id_satuan');
    }
}
