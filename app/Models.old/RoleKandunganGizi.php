<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleKandunganGizi extends Model
{
    use HasFactory;
    protected $table = 'tb_role_kandungan_gizi';
    protected $fillable = ['id_kandungan_gizi', 'id_bahan','jumlah'];
}
