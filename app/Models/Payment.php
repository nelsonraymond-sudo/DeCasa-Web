<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $table = 'payment';
    protected $primaryKey = 'id_metode'; 
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
}