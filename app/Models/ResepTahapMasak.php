<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResepTahapMasak extends Model
{
    use HasFactory;

    protected $table = 'tb_resep_tahap_masak';

    protected $fillable = [
        'id_resep',
        'tahap',
        'keterangan',
        'id_bahan',
    ];

    protected $casts = [
        'id_bahan' => 'array',
    ];

    public function resep()
    {
        return $this->belongsTo(Resep::class, 'id_resep');
    }
}
