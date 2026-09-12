<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BKUArsip extends Model
{
    protected $table = 'tb_bku_arsip';

    protected $fillable = [
        'tanggal',
        'no_bukti',
        'uraian',
        'debit',
        'kredit',
        'saldo',
    ];
}
