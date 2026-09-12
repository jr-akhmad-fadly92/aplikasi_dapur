<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbPoBahan extends Model
{
    use HasFactory;
    protected $table = 'tb_po_bahan';
    protected $fillable = [
        'id_po',
        'id_bahan', 
        'jumlah_bahan', 
        'satuan', 
        'jumlah_po', 
        'tanggal_kedatangan', 
        'tanggal_digunakan',
        'id_rincian_bahan',
        'keterangan',
        'buffer',
        'jumlah_box',
        'id_rincian_kontrak'];

        public function tbMasterBahan()
        {
            return $this->belongsTo('App\Models\TbMasterBahan', 'id_bahan', 'id');
        }

        public function tbPo()
        {
            return $this->belongsTo('App\Models\TbPo', 'id_po', 'id');
        }
        public function tbRincianBahan()
        {
            return $this->belongsTo('App\Models\TbRincianBahan', 'id_rincian_bahan', 'id');
        }

        public function tbPenerimaan()
        {
            return $this->hasMany('App\Models\TbPenerimaan', 'id_barang_po', 'id');
        }

        public function bahan()
        {
            return $this->belongsTo('App\Models\TbMasterBahan', 'id_bahan', 'id');
        }
}
