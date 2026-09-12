<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbRumusPerhitunganKarbo extends Model
{
    use HasFactory;
    /**
     * Nama tabel
     */
    protected $table = 'tb_rumus_perhitungan_karbo';

    /**
     * Kolom yang bisa diisi (mass assignment)
     */
    protected $fillable = [
        'id_menu',
        'karbo_porsi_a',
        'karbo_porsi_b',
        'karbo_kebutuhan_beras_a',
        'karbo_kebutuhan_beras_b',
        'karbo_kebutuhan_beras_total',
        'karbo_kebutuhan_tray',
        'karbo_kebutuhan_steamer',
        'karbo_kebutuhan_pintu_steamer',
        'karbo_kebutuhan_air',
        'karbo_hasil_produksi',
        'karbo_hasil_produksi_kg',
        'karbo_hitungan_cuci_beras',
    ];
}
