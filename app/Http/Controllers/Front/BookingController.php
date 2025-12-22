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
            'id_metode'   => 'required'
        ]);

        try {
            $userId = Auth::user()->id_user; 

            
            $result = DB::select('CALL create_booking(?, ?, ?, ?, ?)', [
                $userId,
                $request->id_properti,
                $request->checkin,
                $request->checkout,
                $request->id_metode
            ]);

            $response = $result[0];

            if (isset($response->message) && str_contains($response->message, 'ERROR')) {
                return back()->with('error', $response->message)->withInput();
            }

            $newTrxId = $response->id_transaksi ?? null;

            return redirect()->route('customer.booking.success', ['id' => $newTrxId])
                             ->with('success', $response->message);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses booking: ' . $e->getMessage());
        }
    }
}