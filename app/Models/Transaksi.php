<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;
    protected $table = 'transaksi';
    protected $primaryKey = 'id_trans';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
}