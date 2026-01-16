<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('customer.profil', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'     => 'required|string|max:100', 
            'email'    => 'required|email|max:100',
            'no_hp'    => 'nullable|string|max:20', 
            'password' => 'nullable|min:6|confirmed',
        ]);

        try {
            if ($request->filled('password')) {
                DB::table('users')
                    ->where('id_user', $user->id_user)
                    ->update([
                        'pass' => Hash::make($request->password), 
                        'updated_at' => now()
                    ]);
            }

            $no_hp_to_update = $request->no_hp ?? $user->no_hp;

            $result = DB::select("CALL update_profile(?, ?, ?, ?)", [
                $user->id_user,
                $request->name,  
                $request->email,
                $no_hp_to_update
            ]);

            $message = $result[0]->message ?? 'An error has occurred.';

            if (str_contains($message, 'ERROR')) {
                return back()->with('error', $message);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'System Error: ' . $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/'); 
    }
}