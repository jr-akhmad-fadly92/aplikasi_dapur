<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbKontrak extends Model
{
    use HasFactory;
    protected $table = 'tb_kontrak';
    protected $fillable = [
        'nomor_kontrak',
        'id_supplier',
        'awal_kontrak',
        'akhir_kontrak',
        'cara_pembayaran',
        'nomor_rekening_pembayaran',
        'nama_rekening',
        'status'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id');
    }

    public function rincianKontrak()
    {
        return $this->hasMany(TbRincianKontrak::class, 'id_kontrak');
    }
}
