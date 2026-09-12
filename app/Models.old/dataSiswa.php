<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class dataSiswa extends Model
{
    use HasFactory;
    protected $table = 'tb_data_siswa';
    protected $fillable = [
        'id',
        'id_tb_data_sekolah',
        'tahun_ajaran',
        'semester',
        'jumlah_a',
        'jumlah_b',
    ];
    public function tb_data_sekolah()
    {
        return $this->belongsTo(DataSekolah::class, 'id_tb_data_sekolah', 'id');
    }
}
