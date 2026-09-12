<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class golonganBahan extends Model
{
    use HasFactory;
    protected $table = 'golongan_bahan';
    protected $fillable = ['golongan_id', 'bahan_id', 'keterangan'];
    public function golongan()
    {
        return $this->belongsTo(Golongan::class, 'golongan_id');
    }
    public function bahan()
    {
        return $this->belongsTo(TbMasterBahan::class, 'bahan_id');
    }
    
}
