<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailFasilitas extends Model
{
    use HasFactory;

    protected $table = 'detailfasilitas';
    
    // HAPUS atau KOMENTARI baris-baris di bawah ini karena kita kembali ke default Laravel:
    // protected $primaryKey = 'id_detail';
    // public $incrementing = false;
    // protected $keyType = 'string';

    // Jika kamu ingin eksplisit (opsional, karena defaultnya sudah begini):
    protected $primaryKey = 'id'; 
    
    protected $guarded = [];

    public function properti()
    {
        return $this->belongsTo(Properti::class, 'id_properti', 'id_properti');
    }
    
    public function fasilitas()
    {
        return $this->belongsTo(Fasilitas::class, 'id_fasilitas', 'id_fasilitas');
    }
}