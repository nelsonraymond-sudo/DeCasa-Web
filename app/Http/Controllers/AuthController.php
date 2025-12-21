<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AuthController extends Controller
{
    // --- VIEW ---
    public function showLogin()
    {
        return view('auth.login'); 
    }

    public function showRegister()
    {
        return view('auth.register'); 
    }

    // --- PROSES LOGIN (Tidak Berubah) ---
    public function processLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required', 
            'password' => 'required',
        ]);

        $credentials = [
            'email'    => $request->email,
            'password' => $request->password 
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $role = Auth::user()->role;

            if ($role === 'admin') { 
                return redirect()->intended(route('admin.dashboard'));
            } else if ($role === 'customer') {
                return redirect()->intended('/'); 
            } else {
                Auth::logout();
                return back()->withErrors(['email' => 'Role tidak dikenali.']);
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    // --- PROSES REGISTER (ADMIN) ---
    // Diubah untuk menangani Form Register Admin sesuai permintaan
    public function processRegister(Request $request)
    {
        // 1. Validasi Input (Sesuai name di register.blade.php)
        $request->validate([
            'nm_user'  => 'required|max:100',
            'email'    => 'required|email|unique:users,email',
            'no_hp'    => 'required',
            'password' => 'required|min:6|confirmed', // 'confirmed' akan cek input name="password_confirmation"
        ]);

        try {
            // 2. Generate ID Admin Otomatis (A0001, dst) menggunakan Function Database
            // Kita pakai function generate_id_admin() yang sudah diperbaiki sebelumnya
            $newIdObj = DB::select("SELECT generate_id_admin() AS id");
            
            // Cek jika function mengembalikan hasil
            if (empty($newIdObj)) {
                throw new \Exception("Gagal generate ID Admin dari database.");
            }
            
            $newId = $newIdObj[0]->id;

            // 3. Simpan ke Tabel Users
            User::create([
                'id_user' => $newId,                  // ID dari function database
                'nm_user' => $request->nm_user,       // Input dari form
                'email'   => $request->email,         // Input dari form
                'pass'    => Hash::make($request->password), // Hashing password
                'role'    => 'admin',                 // SET ROLE JADI ADMIN
                'no_hp'   => $request->no_hp,         // Input dari form
            ]);
            
            // 4. Redirect ke Login dengan pesan sukses
            return redirect()->route('login')->with('success', 'Admin Berhasil Didaftarkan dengan ID: ' . $newId . '. Silakan Login.');

        } catch (\Exception $e) {
            // Jika error, kembalikan ke form register dengan pesan error
            return back()->withErrors(['msg' => 'Gagal Register: ' . $e->getMessage()])->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}