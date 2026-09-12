<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    // Nama tabel di database
    protected $table = 'tb_menu';

    // Kolom yang bisa diisi
    protected $fillable = ['menu', 'karbohidrat', 'protein', 'sayur', 'buah', 'susu', 'nama_pengaju', 'tanggal_pengajuan', 'tanggal_kirim', 'nama_acc', 'tanggal_acc', 'status_pengajuan','nomor_pengajuan','hari_kirim', 'golongan'];

    // Relasi: setiap resep memiliki satu karbohidrat
    public function karbohidrat()
    {
        return $this->belongsTo(Menu::class, 'karbohidrat');
    }

    // Relasi: setiap resep memiliki satu protein
    public function protein()
    {
        return $this->belongsTo(Menu::class, 'protein');
    }

    // Relasi: setiap resep memiliki satu sayur
    public function sayur()
    {
        return $this->belongsTo(Menu::class, 'sayur');
    }

    // Relasi: setiap resep memiliki satu buah
    public function buah()
    {
        return $this->belongsTo(Menu::class, 'buah');
    }

    // Relasi: setiap resep memiliki satu susu
    public function susu()
    {
        return $this->belongsTo(Menu::class, 'susu');
    }

    public function surat_jalan()
    {
        return $this->hasMany(suratJalan::class, 'id_menu_harian', 'id');
    }

    public function resepKarbohidrat()
    {
        return $this->belongsTo(Resep::class, 'karbohidrat', 'id');
    }

    public function resepProtein()
    {
        return $this->belongsTo(Resep::class, 'protein', 'id');
    }

    public function resepSayur()
    {
        return $this->belongsTo(Resep::class, 'sayur', 'id');
    }

    public function resepBuah()
    {
        return $this->belongsTo(Resep::class, 'buah', 'id');
    }

    public function resepSusu()
    {
        return $this->belongsTo(Resep::class, 'susu', 'id');
    }

    public function rincianMenuKarbohidrat()
    {
        return $this->hasMany(rincian_menu_harian::class, 'id_menu_harian')
                    ->where('id_resep', '=', function ($query) {
                        // Mengambil nilai karbohidrat pada menu yang sedang diambil
                        $query->select('karbohidrat')->from('tb_menu')->whereColumn('id', 'id_menu_harian');
                    });
    }
    public function rincianMenuProtein()
    {
        return $this->hasMany(rincian_menu_harian::class, 'id_menu_harian')
                    ->where('id_resep', '=', function ($query) {
                        // Mengambil nilai karbohidrat pada menu yang sedang diambil
                        $query->select('protein')->from('tb_menu')->whereColumn('id', 'id_menu_harian');
                    });
    }
    public function rincianMenuSayur()
    {
        return $this->hasMany(rincian_menu_harian::class, 'id_menu_harian')
                    ->where('id_resep', '=', function ($query) {
                        // Mengambil nilai karbohidrat pada menu yang sedang diambil
                        $query->select('sayur')->from('tb_menu')->whereColumn('id', 'id_menu_harian');
                    });
    }
    public function rincianMenuBuah()
    {
        return $this->hasMany(rincian_menu_harian::class, 'id_menu_harian')
                    ->where('id_resep', '=', function ($query) {
                        // Mengambil nilai karbohidrat pada menu yang sedang diambil
                        $query->select('buah')->from('tb_menu')->whereColumn('id', 'id_menu_harian');
                    });
    }
    public function rincianMenuSusu()
    {
        return $this->hasMany(rincian_menu_harian::class, 'id_menu_harian')
                    ->where('id_resep', '=', function ($query) {
                        // Mengambil nilai karbohidrat pada menu yang sedang diambil
                        $query->select('susu')->from('tb_menu')->whereColumn('id', 'id_menu_harian');
                    });
    }

    /**
     * Scope: filter menus by exact delivery date (tanggal_kirim)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|\DateTimeInterface $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('tanggal_kirim', $date);
    }

    /**
     * Scope: filter menus by date range (tanggal_kirim)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|\DateTimeInterface $start
     * @param string|\DateTimeInterface $end
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDateRange($query, $start, $end)
    {
        return $query->whereBetween('tanggal_kirim', [$start, $end]);
    }

    /**
     * Scope: only approved menus (optional if status_pengajuan used)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('status_pengajuan', 'disetujui');
    }
}
