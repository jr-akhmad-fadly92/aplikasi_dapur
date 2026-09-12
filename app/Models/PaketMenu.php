<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketMenu extends Model
{
    use HasFactory;

    protected $table = 'tb_paket_menu';

    protected $fillable = [
        'paket',
        'resep_karbo',
        'resep_protein',
        'resep_sayur',
        'resep_buah',
        'resep_suplemen',
        'berat_mentah_karbo_a',
        'berat_mentah_karbo_b',
        'berat_mentah_protein_a',
        'berat_mentah_protein_b',
        'berat_mentah_sayur1_a',
        'berat_mentah_sayur2_a',
        'berat_mentah_sayur3_a',
        'berat_mentah_sayur4_a',
        'berat_mentah_sayur1_b',
        'berat_mentah_sayur2_b',
        'berat_mentah_sayur3_b',
        'berat_mentah_sayur4_b',
        'berat_mentah_buah_a',
        'berat_mentah_buah_b',
        'berat_mentah_suplemen_a',
        'berat_mentah_suplemen_b'
    ];
}
