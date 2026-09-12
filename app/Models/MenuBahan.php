<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TbMasterBahan;
use App\Models\TbSatuan;

class MenuBahan extends Model
{
    use HasFactory;

    protected $table = 'tb_menu_bahan';

    protected $fillable = [
        'menu_id',
        'bahan_id',
        'jumlah',
        'id_satuan',
        'status_bahan_baku'
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function bahan()
    {
        return $this->belongsTo(TbMasterBahan::class, 'bahan_id');
    }

    public function satuan()
    {
        return $this->belongsTo(TbSatuan::class, 'id_satuan');
    }

    
}
