<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RincianMasterBahan extends Model
{
    use HasFactory;
    // nama tabel
    protected $table = 'tb_rincian_master_bahan';

    // primary key
    protected $primaryKey = 'id';

    // kolom yang bisa diisi (mass assignment)
    protected $fillable = [
        'id_bahan',
        'jumlah',
        'satuan',
        'status',
    ];
}
