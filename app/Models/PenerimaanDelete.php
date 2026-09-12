<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenerimaanDelete extends Model
{
    use HasFactory;
    protected $table = 'tb_penerimaan_delete';

    protected $fillable = [
        'id_barang_po',
        'jumlah_datang',
        'jumlah_berat',
        'satuan_berat',
        'status',
        'keterangan',
        'qr_code_wadah',
        'nama_penerima',
    ];
}
