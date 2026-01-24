<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('admin.setting.index', compact('user'));
    }

    public function update(Request $request)
    {
        
        $user = Auth::user();

        $request->validate([
            'nm_user' => 'required|string|max:100',
            'email' => 'required|email',
            'no_hp' => 'required|string|max:20',
            'password' => 'nullable|min:6|confirmed'
        ]);

        try {
            if ($request->filled('password')) {
                $user->pass = Hash::make($request->password);
                $user->save();
            }

            $result = DB::select("CALL update_profile(?, ?, ?, ?)", [
                $user->id_user,
                $request->nm_user,
                $request->email,
                $request->no_hp
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
}