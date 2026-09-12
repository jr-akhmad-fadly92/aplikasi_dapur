<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class rincian_menu_harian extends Model
{
    use HasFactory;
    // Nama tabel di database
    protected $table = 'rincian_menu_harian';

    // Kolom yang bisa diisi
    protected $fillable = ['id', 'id_menu_harian', 'id_resep', 'id_bahan','jumlah','bumbu',
        'harga',
        'total_harga',
        'jumlah_box',
        'id_satuan',
        'id_kontrak'];

    public function bahan()
    {
        return $this->belongsTo(TbMasterBahan::class, 'id_bahan');
    }

    public function tbPoBahan()
    {
        return $this->hasMany(TbPoBahan::class, 'id_rincian_bahan', 'id');
    }
}
