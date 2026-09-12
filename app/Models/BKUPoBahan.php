<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BKUPoBahan extends Model
{
    protected $table = 'tb_po_bahan';
    protected $fillable = [
        'id_po', 'id_bahan', 'jumlah_bahan', 'satuan', 'jumlah_po',
        'buffer', 'jumlah_box', 'keterangan', 'id_rincian_kontrak',
        'id_kontrak', 'id_rincian_bahan', 'tanggal_kedatangan', 'tanggal_digunakan'
    ];

    public function po()
    {
        return $this->belongsTo(BKUPo::class, 'id_po');
    }

    public function masterBahan()
    {
        return $this->hasOne(BKUMasterBahan::class, 'satuan_bahan', 'satuan');
    }
}
