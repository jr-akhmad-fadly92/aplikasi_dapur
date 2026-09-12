<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DataSekolah;

class sekolahAktif extends Model
{
    use HasFactory;
    protected $table = 'sekolah_aktif';
    protected $fillable = [
        'id_tb_data_sekolah',
        'senin',
        'selasa',
        'rabu',
        'kamis',
        'jumat',
        'sabtu',
        'minggu',
        'status'
    ];

    public function sekolah()
    {
        return $this->belongsTo(DataSekolah::class, 'id_tb_data_sekolah', 'id');
    }
}
