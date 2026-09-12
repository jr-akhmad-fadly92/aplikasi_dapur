<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class rincian_sekolah extends Model
{
    use HasFactory;
    // Nama tabel di database
    protected $table = 'rincian_sekolah';

    // Kolom yang bisa diisi
    protected $fillable = [
        'id', 
        'id_menu_harian', 
        'id_sekolah',
        'jumlah_penerima_total',
        'jumlah_penerima_a',
        'jumlah_penerima_b',

        'status'];

    public function data_sekolah()
    {
        return $this->belongsTo(DataSekolah::class, 'id_sekolah', 'id');
    }

    /**
     * Scope: filter by array of menu harian IDs
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $menuIds
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForMenus($query, array $menuIds)
    {
        if (empty($menuIds)) {
            return $query->whereRaw('1 = 0');
        }
        return $query->whereIn('id_menu_harian', $menuIds);
    }

    /**
     * Scope: only active rows (status = 1)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope: filter by sekolah id
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $sekolahId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForSekolah($query, $sekolahId)
    {
        return $query->where('id_sekolah', $sekolahId);
    }
}
