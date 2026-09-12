<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterBantuan extends Model
{
    use HasFactory;
    protected $table = 'tb_master_bantuan'; // custom nama tabel

    protected $fillable = [
        'bantuan_pangan_A',
        'bantuan_pangan_B',
        'bantuan_operasional',
        'bantuan_infra',
    ];
}
