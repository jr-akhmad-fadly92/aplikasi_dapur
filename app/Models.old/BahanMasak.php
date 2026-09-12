<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TbMasterBahan;
use App\Models\TbSatuan;

class BahanMasak extends Model
{
    use HasFactory;
    protected $table = 'tb_bahan_masak';

    protected $fillable = [
        'bahan_id',
        'jumlah',
        'id_satuan',
        'kategori_bahan',
        'status_bahan',
    ];

    public function tb_master_bahan()
    {
        return $this->belongsTo(TbMasterBahan::class, 'bahan_id');
    }

    public function tb_satuan()
    {
        return $this->belongsTo(TbSatuan::class, 'id_satuan');
    }
}
