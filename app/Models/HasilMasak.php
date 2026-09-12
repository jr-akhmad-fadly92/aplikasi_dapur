<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilMasak extends Model
{
    use HasFactory;
    protected $table = 'tb_hasil_masak';
    protected $fillable = ['id_menu', 'id_komponen_sehat', 'jumlah', 'status', 'waktu_matang'];
}
