<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'id_user';
    public $keyType = 'string'; 
    public $incrementing = false;

    protected $fillable = [
        'id_user', 'nm_user', 'email', 'pass', 'role', 'no_hp',
    ];

    protected $hidden = [
        'pass', 'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->pass; 
    }
}