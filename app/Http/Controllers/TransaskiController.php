<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Properti; // Pastikan model ini ada

class TransaksiController extends Controller
{
    // Function untuk memproses Booking
    public function store(Request $request)
    {
        // 1. Validasi Input (Pastikan data yang dikirim user lengkap)
        $request->validate([
            'id_properti' => 'required',
            'checkin'     => 'required|date|after:today', // Checkin harus setelah hari ini
            'checkout'    => 'required|date|after:checkin', // Checkout harus setelah checkin
            'id_metode'   => 'required',
        ]);

        try {
            // 2. Siapkan Data untuk Procedure
            // Karena login belum dibuat, untuk testing kita tembak manual dulu ID User-nya
            // Nanti kalau sudah ada login, ganti jadi: Auth::user()->id_user;
            $id_user     = 'U0001'; // <-- HARDCODE DULU UNTUK TESTING (Sesuai ID di screenshot Anda)
            
            $id_properti = $request->id_properti;
            $checkin     = $request->checkin;
            $checkout    = $request->checkout;
            $id_metode   = $request->id_metode;

            // 3. Panggil Stored Procedure: create_booking
            // Urutan: id_user, id_properti, checkin, checkout, id_metode
            DB::statement("CALL create_booking(?, ?, ?, ?, ?)", [
                $id_user,
                $id_properti,
                $checkin,
                $checkout,
                $id_metode
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Booking berhasil dibuat via Stored Procedure!'
            ]);

        } catch (\Exception $e) {
            // Tangkap error dari Database (misal: Properti penuh, tanggal salah, dll)
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal Booking: ' . $e->getMessage()
            ], 500);
        }
    }
}