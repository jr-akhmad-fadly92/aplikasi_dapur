<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RumusPerhitunganProtein extends Model
{
    use HasFactory;

    // Nama tabel (karena bukan plural Laravel default)
    protected $table = 'tb_rumus_perhitungan_protein';

    // Kolom yang boleh diisi (fillable)
    protected $fillable = [
        'id_menu',
        'protein_porsi_a',
        'protein_porsi_b',
        'kebutuhan_total_matang',
        'kebutuhan_protein_a',
        'kebutuhan_protein_b',
        'penyusutan_protein_a',
        'penyusutan_protein_b',
        'kebutuhan_matang_a',
        'kebutuhan_matang_b',
        'kebutuhan_matang_realisasi',
        'kebutuhan_total_mentah',
        'kapasitas_tilting',
        'jumlah_masak',
        'status',
    ];
}
