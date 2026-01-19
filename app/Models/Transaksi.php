<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Properti;
use App\Models\Payment;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';
    protected $fillable = [
        'id_trans', 'id_user', 'id_properti', 'id_metode', 'tgl_trans',
        'checkin', 'checkout', 'total_harga', 'durasi', 'status'
    ];
    protected $primaryKey = 'id_trans';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    public function properti()
    {
        return $this->belongsTo(Properti::class, 'id_properti', 'id_properti');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'id_metode', 'id_metode');
    }
}