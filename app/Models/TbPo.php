<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbPo extends Model
{
    use HasFactory;
    protected $table = 'tb_po';
    protected $fillable = [
        'nomor_po',
        'tanggal_po',
        'status_po',
        'tanggal_pengajuan',
        'tanggal_approve',
        'id_kontrak',
        'id_karyawan',
        'manual',
        'revisi'
    ];

    public function tbPoBahan()
    {
        return $this->hasMany(TbPoBahan::class, 'id_po', 'id');
    }
    public function tbKontrak()
    {
        return $this->belongsTo(TbKontrak::class, 'id_kontrak', 'id');
    }
}
