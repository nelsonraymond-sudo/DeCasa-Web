<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }
    public function showRegister() { return view('auth.register'); }

    public function processLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required', 
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $role = Auth::user()->role;

            if ($role === 'admin') { 
                return redirect()->intended(route('admin.dashboard'));
            } else if ($role === 'customer') {
                return redirect()->intended('/'); 
            } else {
                Auth::logout();
                return back()->withErrors(['email' => 'Role tidak valid.']);
            }
        }

        return back()->withErrors(['email' => 'Email atau password salah.']);
    }

    public function processRegister(Request $request)
    {
        $request->validate([
            'nm_user'  => 'required|max:100',
            'email'    => 'required|email|unique:users,email',
            'no_hp'    => 'required',
            'password' => 'required|min:6|confirmed', 
        ]);

        try {
            $query = DB::select("SELECT generate_id_admin() AS id");
            
            if (empty($query)) {
                throw new \Exception("Database gagal men-generate ID Admin.");
            }
            
            $newId = $query[0]->id;

    
            User::create([
                'id_user' => $newId,         
                'nm_user' => $request->nm_user,
                'email'   => $request->email,
                'pass'    => Hash::make($request->password), 
                'role'    => 'admin',          
                'no_hp'   => $request->no_hp,
            ]);
            
            return redirect()->route('login')->with('success', "Admin terdaftar dengan ID: $newId. Silakan Login.");

        } catch (\Exception $e) {
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