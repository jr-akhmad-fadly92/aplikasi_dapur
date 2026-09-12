<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CekRincianMenuHarian extends Model
{
    protected $table = 'rincian_menu_harian';
    protected $fillable = ['id_menu_harian', 'id_resep', 'id_bahan', 'jumlah', 'bumbu', 'harga', 'total_harga', 'id_satuan'];

    public function resep()
    {
        return $this->belongsTo(CekResep::class, 'id_resep');
    }

    public function bahan()
    {
        return $this->belongsTo(CekMasterBahan::class, 'id_bahan');
    }
}
