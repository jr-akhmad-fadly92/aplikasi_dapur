<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriMenu extends Model
{
    use HasFactory;
    protected $table = 'tb_histori_menu';
    protected $fillable = [
        'id_menu',
        'kode',
        'total_porsi',
        'hasil_menu_karbohidrat',
        'hasil_porsi_karbohidrat',
        'hasil_menu_protein',
        'hasil_porsi_protein',
        'hasil_menu_sayur',
        'hasil_porsi_sayur',
        'hasil_menu_buah',
        'hasil_porsi_buah',
        'hasil_menu_susu',
        'hasil_porsi_susu',
        'status',
        'waktu_mulai_masak'
    ];
}
