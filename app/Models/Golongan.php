<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Golongan extends Model
{
    use HasFactory;
    protected $table = 'golongan';
    protected $fillable = ['parent_id', 'no', 'golongan_path', 'golongan', 'keterangan'];
    public function parent()
    {
        return $this->belongsTo(Golongan::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(Golongan::class, 'parent_id');
    }
}
