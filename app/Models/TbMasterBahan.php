<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbMasterBahan extends Model
{
    use HasFactory;
    protected $table = 'tb_master_bahan';

    // Tentukan kolom-kolom yang dapat diisi (fillable)
    protected $fillable = [
        'bahan',
        'gramasi',
        'satuan_gudang',
        'satuan_bahan',
        'jenis'
    ];

    /**
     * Relasi ke tabel tb_satuan untuk kolom satuan_gudang.
     */
    public function satuanGudang()
    {
        return $this->belongsTo(TbSatuan::class, 'satuan_gudang');
    }

    /**
     * Relasi ke tabel tb_satuan untuk kolom satuan_bahan.
     */
    public function satuanBahan()
    {
        return $this->belongsTo(TbSatuan::class, 'satuan_bahan');
    }

    public function tbPoBahan()
    {
        return $this->hasMany('App\Models\TbPoBahan', 'id_bahan', 'id');
    }
}
