<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePenugasan extends Model
{
    use HasFactory;
    // Nama tabel
    protected $table = 'tb_role_penugasan';

  
    // Kolom yang bisa diisi
    protected $fillable = [
        'id_penugasan',
        'tanggal',
        'tanggal_selesai',
    ];
}
