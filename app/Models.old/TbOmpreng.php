<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbOmpreng extends Model
{
    use HasFactory;
    protected $table = "tb_ompreng";
    protected $fillable = [
        'id_dapur',
        'kode_ompreng',
        'jenis',
        'status',
        'nomor'
    ];

    public function tb_ompreng_transaksi()
    {
        return $this->hasMany(tbOmprengTransaksi::class, 'kode_ompreng', 'kode_ompreng');
    }
}
