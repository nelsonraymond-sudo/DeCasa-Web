<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB; 

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users'; 
    protected $primaryKey = 'id_user'; 
    public $incrementing = false; 
    protected $keyType = 'string';

    protected $fillable = [
        'id_user',
        'nm_user',
        'email',
        'pass', 
        'role',
        'no_hp',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'pass', // Sembunyikan pass saat return JSON
        'remember_token',
    ];

    /**
     * Get the password for the user.
     * Laravel defaultnya cari kolom 'password', kita override ke 'pass'
     */
    public function getAuthPassword()
    {
        return $this->pass;
    }

    
    protected static function booted()
    {
        static::creating(function ($user) {
            if ($user->role === 'customer' && empty($user->id_user)) {
                
                $query = DB::select("SELECT generate_id_customer() as new_id");
                
                $user->id_user = $query[0]->new_id;
            }
            
             if ($user->role === 'admin' && empty($user->id_user)) {
                 $query = DB::select("SELECT generate_id_admin() as new_id"); 
                 $user->id_user = $query[0]->new_id;
             }
        });
    }
}