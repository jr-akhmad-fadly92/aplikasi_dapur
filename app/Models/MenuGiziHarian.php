<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuGiziHarian extends Model
{
    use HasFactory;

    protected $table = 'tb_menu_gizi_harian';

    protected $fillable = [
        'id_menu',
        'energi',
        'protein',
        'lemak',
        'karbohidrat',
        'serat',
        'natrium',
    ];

    protected $casts = [
        'energi' => 'decimal:2',
        'protein' => 'decimal:2',
        'lemak' => 'decimal:2',
        'karbohidrat' => 'decimal:2',
        'serat' => 'decimal:2',
        'natrium' => 'decimal:2',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'id_menu', 'id');
    }
}
