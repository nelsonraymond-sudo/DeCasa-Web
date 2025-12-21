<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB; // <--- JANGAN LUPA INI

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users'; // Pastikan nama tabel sesuai
    protected $primaryKey = 'id_user'; // Primary key kita bukan 'id', tapi 'id_user'
    public $incrementing = false; // Karena ID kita String (U0001), bukan Auto Increment Integer
    protected $keyType = 'string';

    protected $fillable = [
        'id_user',
        'nm_user',
        'email',
        'pass', // Sesuaikan nama kolom password di DB
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

    /**
     * LOGIC UTAMA: Panggil Function SQL saat Creating
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            // Jika role-nya customer dan id_user belum diisi manual
            if ($user->role === 'customer' && empty($user->id_user)) {
                
                // Panggil Function SQL: generate_id_customer()
                $query = DB::select("SELECT generate_id_customer() as new_id");
                
                // Masukkan ID baru ke model sebelum disimpan
                $user->id_user = $query[0]->new_id;
            }
            
            // Opsional: Jika role admin, panggil function generate_id_admin (jika ada)
             if ($user->role === 'admin' && empty($user->id_user)) {
                 $query = DB::select("SELECT generate_id_admin() as new_id"); // Asumsi function admin ada
                 $user->id_user = $query[0]->new_id;
             }
        });
    }
}