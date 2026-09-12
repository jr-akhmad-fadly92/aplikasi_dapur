<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BKKMasterBahan1 extends Model
{
    protected $table = 'tb_master_bahan';
    protected $fillable = [
        'bahan','gramasi','satuan_gudang','satuan_bahan','jenis'
    ];
}
