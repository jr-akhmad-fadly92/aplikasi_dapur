<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BKKPoBahan1 extends Model
{
    protected $table = 'tb_po_bahan';
    protected $fillable = [
        'id_po','id_bahan','jumlah_bahan','satuan','jumlah_po','buffer',
        'jumlah_box','keterangan','id_rincian_kontrak','id_kontrak',
        'id_rincian_bahan','tanggal_kedatangan','tanggal_digunakan'
    ];

    public function masterBahan()
    {
        return $this->hasOne(BKKMasterBahan1::class, 'satuan_bahan', 'satuan');
    }
}
