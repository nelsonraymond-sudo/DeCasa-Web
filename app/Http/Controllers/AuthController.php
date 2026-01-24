<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator; 
use App\Models\User;

class AuthController extends Controller
{
    // --- WEB METHODS ---

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
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $role = Auth::user()->role;

            if ($role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            } else if ($role === 'customer') {
                return redirect()->intended(route('home'));
            } else {
                Auth::logout();
                return back()->withErrors(['email' => 'Invalid role.']);
            }
        }

        return back()->withErrors(['email' => 'Incorrect email or password.']);
    }

    public function processRegister(Request $request)
    {
        $request->validate([
            'nm_user' => 'required|max:100',
            'email' => 'required|email|unique:users,email',
            'no_hp' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        try {
            $query = DB::select("SELECT generate_id_customer() AS id");

            if (empty($query)) {
                throw new \Exception("Database gagal men-generate ID Customer.");
            }

            $newId = $query[0]->id;

            User::create([
                'id_user' => $newId,
                'nm_user' => $request->nm_user,
                'email' => $request->email,
                'pass' => Hash::make($request->password),
                'role' => 'customer',
                'no_hp' => $request->no_hp,
            ]);

            return redirect()->route('login')->with('success', "You are registered. Please login.");

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

    // --- API METHODS ---

    public function loginApi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login success',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'data' => $user
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email or Password incorrect'
        ], 401);
    } 

    public function registerApi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nm_user' => 'required|max:100',
            'email' => 'required|email|unique:users,email',
            'no_hp' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $query = DB::select("SELECT generate_id_customer() AS id");

            if (empty($query)) {
                throw new \Exception("Database gagal men-generate ID Customer.");
            }
            $newId = $query[0]->id;

            $user = User::create([
                'id_user' => $newId,
                'nm_user' => $request->nm_user,
                'email' => $request->email,
                'pass' => Hash::make($request->password),
                'role' => 'customer',
                'no_hp' => $request->no_hp,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'data' => $user
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 500); 
        }
    }
}