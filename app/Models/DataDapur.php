<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataDapur extends Model
{
    use HasFactory;

    protected $table = 'tb_data_dapur';

    protected $fillable = [
        'nama_dapur',
        'alamat_dapur',
        'nomor_dapur',
        'ip_dapur',
        'kota',
        'provinsi',
        'kelurahan',
        'kecamatan',
        'no_telp',
        'email',
        'pemilik',
        'kepala_dapur',
        'admin_dapur',
        'ahli_gizi',
        'ahli_akuntan',
    ];

    public function tb_kabkota()
    {
        return $this->belongsTo(KabKota::class, 'kota', 'id');
    }

    public function tb_provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'provinsi', 'id');
    }

    /**
     * Helper untuk mengambil record dapur atau fallback dengan nilai default
     * agar tidak memicu error ketika tabel kosong.
     */
    public static function firstOrDefault(): self
    {
        $instance = self::first();
        if ($instance) {
            return $instance;
        }

        return new self([
            'nama_dapur' => 'Dapur',
            'alamat_dapur' => '-',
            'nomor_dapur' => '000',
            'ip_dapur' => null,
            'kota' => '-',
            'provinsi' => '-',
            'kelurahan' => '-',
            'kecamatan' => '-',
            'no_telp' => 0,
            'email' => null,
            'pemilik' => '-',
            'kepala_dapur' => '-',
            'admin_dapur' => '-',
            'ahli_gizi' => '-',
            'ahli_akuntan' => '-',
        ]);
    }
}
