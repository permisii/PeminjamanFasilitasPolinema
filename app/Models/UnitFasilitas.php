<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitFasilitas extends Model
{
    use HasFactory;
    protected $table = 'unit_fasilitas';
    protected $fillable = ["fasilitas_id", 'nama', 'unit', 'luas', "lama_sewa"];

    public function fasilitas()
    {
        return $this->belongsTo(Fasilitas::class);
    }

    public function tarif()
    {
        return $this->hasMany(Tarif::class);
    }
    public function UnitFasilitas()
    {
        return $this->hasMany(UnitFasilitas::class);
    }
}
