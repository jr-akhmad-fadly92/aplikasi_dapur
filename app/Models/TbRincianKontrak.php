<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbRincianKontrak extends Model
{
    use HasFactory;
    protected $table = 'tb_rincian_kontrak';
    protected $fillable = [
        'id_kontrak',
        'id_bahan',
        'merek_bahan',  // update 14 - 6 - 2025
        'harga_bahan',
        'jumlah_bahan',
        'satuan_bahan',
        'status',       // update 14 - 6 - 2025
        'kemasan',       // update 14 - 6 - 2025
    ];

    public function kontrak()
    {
        return $this->belongsTo(TbKontrak::class, 'id_kontrak');
    }

    public function bahan()
    {
        return $this->belongsTo(TbMasterBahan::class, 'id_bahan');
    }
}
