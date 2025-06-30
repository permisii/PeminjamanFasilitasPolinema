<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    use HasFactory;

    protected $fillable = ["kelompok_tarif", "harga", "fasilitas_id", "unit_fasilitas_id"];
    public function fasilitas()
    {
        return $this->belongsTo(Fasilitas::class);
    }

    public function unitFasilitas()
    {
        return $this->belongsTo(UnitFasilitas::class);
    }
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class);
    }
}

