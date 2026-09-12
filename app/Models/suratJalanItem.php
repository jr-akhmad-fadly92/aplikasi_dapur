<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class suratJalanItem extends Model
{
    use HasFactory;
    protected $table = 'surat_jalan_item';
    protected $fillable = [
        'surat_jalan_referensi',
        'rincian_sekolah_id',
        'jumlah_a',
        'jumlah_b',
        'jumlah',
        'status',
        'created_at',
        'updated_at'
    ];
    public function suratJalan()
    {
        return $this->belongsTo(suratJalan::class, 'surat_jalan_referensi', 'referensi');
    }
    /*public function rincianMenuHarian()
    {
        return $this->belongsTo(rincianMenuHarian::class, 'rincian_menu_harian_id', 'id');
    }*/
    public function rincianSekolah()
    {
        return $this->belongsTo(rincian_sekolah::class, 'rincian_sekolah_id');
    }
}
