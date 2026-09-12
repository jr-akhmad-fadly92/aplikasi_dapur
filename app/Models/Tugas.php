<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    use HasFactory;

    protected $table = 'tb_tugas'; // custom nama tabel

    protected $fillable = [
        'id_tugas',
        'kegiatan',
        'terlampir'
    ];
}
