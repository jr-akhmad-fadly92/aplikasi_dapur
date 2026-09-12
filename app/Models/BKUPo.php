<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BKUPo extends Model
{
    protected $table = 'tb_po';
    protected $fillable = [
        'nomor_po', 'tanggal_po', 'status_po', 'tanggal_pengajuan', 
        'tanggal_approve', 'id_kontrak', 'id_karyawan', 'manual'
    ];

    public function bahan()
    {
        return $this->hasMany(BKUPoBahan::class, 'id_po');
    }

    public function getTanggalAttribute() {
        return $this->tanggal_approve;
    }
}
