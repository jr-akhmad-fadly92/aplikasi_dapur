<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuahRumusPerhitunganBuah extends Model // NAMA MODEL UTAMA ANDA
{
    use HasFactory;

    protected $table = 'tb_rumus_perhitungan_buah'; // Nama tabel yang benar
    protected $primaryKey = 'id_menu';
    public $incrementing = false; // Asumsi id_menu adalah string/UUID
    protected $keyType = 'string';

    protected $fillable = [
        'id_menu, buah_porsi_a', 
        'buah_porsi_b',
        'kebutuhan_total_matang', 
        'kebutuhan_buah_a', 
        'kebutuhan_buah_b', 
        'penyusutan_buah_a', 
        'penyusutan_buah_b', 
        'kebutuhan_matang_a', 
        'kebutuhan_matang_b', 
        'kebutuhan_matang_realisasi', 
        'kebutuhan_total_mentah', 
        'kapasitas_tilting', 
        'jumlah_masak', 
        'status', 
    ];

    /**
     * Relasi: Satu SuppPerhitunganSupplemen memiliki banyak SuppRincianMenuTemp.
     * Kunci asing 'id_menu' di SuppRincianMenuTemp merujuk ke 'id_menu' di SuppPerhitunganSupplemen.
     */
    public function rincianMenuTemp()
    {
        return $this->hasMany(BuahRincianMenuTemp::class, 'id_menu', 'id_menu');
    }
}

