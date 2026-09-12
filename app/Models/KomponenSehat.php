<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomponenSehat extends Model
{
    use HasFactory;
    // Nama tabel di database
    protected $table = 'tb_komponen_sehat';

    // Kolom yang bisa diisi
    protected $fillable = ['komponen'];

    // Relasi: satu KomponenSehat bisa digunakan di banyak menu
    public function menu()
    {
        return $this->hasMany(Menu::class, 'id_komponen_sehat');
    }
}
