<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Foto extends Model
{
    use HasFactory;
    protected $table = 'foto';
    protected $primaryKey = 'id_foto';
    protected $guarded = [];
}
