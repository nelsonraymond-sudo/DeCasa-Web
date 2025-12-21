<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'id_properti' => 'required',
            'checkin'     => 'required|date|after_or_equal:today',
            'checkout'    => 'required|date|after:checkin',
            'id_metode'   => 'required' // Pastikan ada input metode bayar
        ]);

        try {
            $userId = Auth::user()->id_user; // Pastikan user login

            // Panggil Stored Procedure: create_booking
            // Urutan param: p_id_user, p_id_properti, p_checkin, p_checkout, p_id_metode
            $result = DB::select('CALL create_booking(?, ?, ?, ?, ?)', [
                $userId,
                $request->id_properti,
                $request->checkin,
                $request->checkout,
                $request->id_metode
            ]);

            // Ambil pesan dari procedure (SELECT ... AS message)
            $response = $result[0];

            if (str_contains($response->message, 'ERROR')) {
                return back()->with('error', $response->message);
            }

            // Jika sukses, redirect ke halaman sukses/pembayaran
            return redirect()->route('customer.booking.success', ['id' => $response->id_transaksi])
                             ->with('success', $response->message);

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}