<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuahRincianMenuTemp extends Model
{
    use HasFactory;

    protected $table = 'tb_rincian_menu_temp';

    protected $fillable = [
        'id_menu',
        'id_resep',
        'id_bahan',
        'jumlah',
        'bumbu',
        'harga',
        'total_harga',
        'id_satuan',
    ];

    /**
     * Relasi: Satu SuppRincianMenuTemp dimiliki oleh satu SuppRumusPerhitunganSupplemen.
     */
    public function rumusPerhitunganBuah()
    {
        return $this->belongsTo(BuahRumusPerhitunganBuah::class, 'id_menu', 'id_menu');
    }

    /**
     * Relasi: Satu SuppRincianMenuTemp terhubung ke satu SuppMasterBahan.
     * Diasumsikan 'id_bahan' di tabel ini merujuk ke 'id' di tabel 'tb_master_bahan'.
     */
    public function masterBahan()
    {
        return $this->belongsTo(BuahMasterBahan::class, 'id_bahan', 'id');
    }

    /**
     * Relasi: Satu SuppRincianMenuTemp terhubung ke satu Resep.
     * Diasumsikan 'id_resep' di tabel ini merujuk ke 'id' di tabel 'tb_resep'.
     */
    public function resep()
    {
        return $this->belongsTo(Resep::class, 'id_resep', 'id');
    }
}
