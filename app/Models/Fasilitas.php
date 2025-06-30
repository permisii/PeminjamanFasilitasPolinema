<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis_fasilitas_id',
        'kode_fasilitas',
        'nama',
        'thumbnail',
        'unit',
        'luas',
        'lama_sewa',
        'fitur',
    ];

    protected $casts = [
        'fitur' => 'array',
    ];
    public function jenisFasilitas()
    {
        return $this->belongsTo(JenisFasilitas::class);
    }

    public function barang()
    {
        return $this->hasMany(Barang::class);
    }

    public function unitFasilitas()
    {
        return $this->hasMany(UnitFasilitas::class);
    }

    public function detailGambarFasilitas()
    {
        return $this->hasMany(DetailGambarFasilitas::class);
    }

    public function tarif()
    {
        return $this->hasMany(Tarif::class, 'fasilitas_id');
    }

    public function tarifUnitFasilitas()
    {
        return $this->hasManyThrough(Tarif::class, UnitFasilitas::class);
    }
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class);
    }

}
