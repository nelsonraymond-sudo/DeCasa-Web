<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Properti; // Pastikan ada Model Properti
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CustomerApiController extends Controller
{
    // 1. LOGIN (Untuk dapat Token)
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Cek manual karena nama kolom password kamu 'pass'
        if (!$user || !Hash::check($request->password, $user->pass)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password salah'
            ], 401);
        }

        // Cek Role (Hanya Customer yang boleh login di Apps)
        if ($user->role !== 'customer') { // Sesuaikan value role di db kamu
             return response()->json(['message' => 'Hanya customer yang bisa login'], 403);
        }

        // Buat Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login Berhasil',
            'data' => [
                'user' => $user,
                'token' => $token // Token ini nanti disimpan di Android
            ]
        ], 200);
    }

    // 2. LIHAT DATA PROPERTI (READ)
    public function getProperti()
    {
        $properti = Properti::all(); // Atau pakai query custom/procedure
        
        return response()->json([
            'success' => true,
            'data' => $properti
        ], 200);
    }

    // 3. BOOKING (CREATE)
    public function booking(Request $request)
    {
        // Ambil user dari token
        $user = $request->user(); 

        // Validasi input dari Android
        $validator = \Validator::make($request->all(), [
            'id_properti' => 'required',
            'tanggal_booking' => 'required|date',
            // tambahkan field lain sesuai tabel transaksi
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            // Simpan ke database (Sesuaikan dengan logic Transaksi kamu)
            // Contoh pakai Query Builder biar aman
            $id_transaksi = 'TRX-' . time(); // Contoh generate ID

            DB::table('transaksi')->insert([
                'id_transaksi' => $id_transaksi,
                'id_user' => $user->id_user, // ID user diambil otomatis dari Token
                'id_properti' => $request->id_properti,
                'tgl_transaksi' => $request->tanggal_booking,
                'status' => 'pending', // Default status
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil dibuat!',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal booking: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // 4. LOGOUT
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout berhasil']);
    }
}