<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login'); 
    }

    public function showRegister()
    {
        return view('auth.register'); 
    }

    public function processLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            } else {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return back()->withErrors([
                    'email' => 'Anda bukan Admin! Akses ditolak.',
                ]);
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function processRegister(Request $request)
    {
        $request->validate([
            'nm_user'  => 'required|max:100',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'no_hp'    => 'required',
        ]);

        try {
            $passwordHash = Hash::make($request->password);

            DB::statement('CALL register_customer(?, ?, ?, ?)', [
                $request->nm_user,
                $request->email,
                $passwordHash,
                $request->no_hp
            ]);
            
            return redirect()->route('login')->with('success', 'Registrasi Berhasil! Silakan Login.');

        } catch (\Exception $e) {
            return back()->withErrors(['msg' => 'Gagal Register: ' . $e->getMessage()]);
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