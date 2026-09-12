<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LaporanMenuHarian extends Model
{
    use HasFactory;

    protected $table = 'laporan_menu_harian';

    protected $fillable = [
        'tgl_kirim',
        'karbo',
        'protein',
        'sayur',
        'buah',
        'susu',
        'porsi',
        'periode'
    ];

    protected $dates = ['tgl_kirim'];

    /**
     * Relasi ke tabel tb_menu
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'tgl_kirim', 'tanggal_kirim');
    }

    /**
     * Scope untuk periode 1-14
     */
    public function scopePeriodePertama($query)
    {
        return $query->where('periode', '1-14');
    }

    /**
     * Scope untuk periode 15-30
     */
    public function scopePeriodeKedua($query)
    {
        return $query->where('periode', '15-30');
    }

    /**
     * Scope untuk bulan tertentu
     */
    public function scopeBulan($query, $bulan, $tahun)
    {
        return $query->whereYear('tgl_kirim', $tahun)
            ->whereMonth('tgl_kirim', $bulan);
    }
}