<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class suratJalan extends Model
{
    
    use HasFactory;
    protected $table = 'surat_jalan';
    protected $fillable = [
        'user_id',
        'no',
        'id_menu_harian',
        'referensi',
        'no_surat_jalan',
        'driver',
        'plat_nomor',
        'status',
        'keterangan',
        'created_at',
        'updated_at',
        'published_at',
        'driver_assistant'
    ];
    public function suratJalanItem()
    {
        return $this->hasMany(suratJalanItem::class, 'surat_jalan_referensi', 'referensi');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'id_menu_harian', 'id');
    }
}
