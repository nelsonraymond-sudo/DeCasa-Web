<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'Customer Profile',
            'data' => $user
        ], 200);
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $request->validate([
            'nm_user' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'no_hp' => 'nullable|string|max:20',
            'password' => 'nullable|min:6|confirmed',
        ]);

        try {
            if ($request->filled('password')) {
                $user->pass = Hash::make($request->password);
            }

            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('profiles', 'public');
                $user->foto = $path;
            }

            $user->save();

            $no_hp_to_update = $request->no_hp ?? $user->no_hp;

            $result = DB::select("CALL update_profile(?, ?, ?, ?)", [
                $user->id_user,
                $request->nm_user,
                $request->email,
                $no_hp_to_update
            ]);

            $message = $result[0]->message ?? 'An error has occurred.';

            if (str_contains($message, 'ERROR')) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 400);
            }

            $user->refresh();

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'System Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
