<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Pastikan baris ini ada jika Properti berbeda namespace, 
// tapi biasanya tidak perlu jika satu folder.
use App\Models\Properti; 

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';
    protected $primaryKey = 'id_trans';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    // --- TAMBAHKAN BAGIAN INI ---
    public function properti()
    {
        // Parameter: (ModelTujuan, FK_di_tabel_transaksi, PK_di_tabel_properti)
        return $this->belongsTo(Properti::class, 'id_properti', 'id_properti');
    }
    // ----------------------------

    // Relasi ke user (biarkan jika sudah ada)
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}