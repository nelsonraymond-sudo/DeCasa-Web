<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Properti extends Model
{
    use HasFactory;

    protected $table = 'properti';
    protected $primaryKey = 'id_properti';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    public function foto()
    {
        return $this->hasMany(Foto::class, 'id_properti', 'id_properti');
    }

    public function fasilitas()
    {
        return $this->hasMany(DetailFasilitas::class, 'id_properti', 'id_properti');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }
}