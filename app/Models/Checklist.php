<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    use HasFactory;

    protected $table = 'tb_checklist_harian';

    protected $fillable = [
        'tanggal',
        'nomor_dokumen',
        'nama_dokumen',
        'kode_form',
        'status',
        'tanggal_cek',
        'ttd',
        'catatan',
        'dilaporan_oleh',
        'diverifikasi_oleh',
        'didistribusi_oleh',
        'pax_a',
        'pax_b',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_cek' => 'date',
    ];
}
