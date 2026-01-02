<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        
        $transaksi = DB::table('view_booking_history') 
            ->where('id_user', $userId)
            ->orderBy('tanggal_book', 'desc')
            ->get();

        return view('customer.dashboard', compact('transaksi'));
    }

    public function showBookingForm($id)
    {
        $properti = DB::table('view_detail_properti')->where('id_properti', $id)->first();
        $payment = DB::table('payment')->get(); 

        if (!$properti) {
            return redirect()->back()->with('error', 'Properties Not Found');
        }

        return view('customer.booking', compact('properti', 'payment'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_properti' => 'required',
            'checkin'     => 'required|date',
            'checkout'    => 'required|date|after:checkin',
            'metode_bayar'=> 'required'
        ]);

        try {
            $idUser = Auth::id();
            
            $result = DB::select("CALL create_booking(?, ?, ?, ?, ?)", [
                $idUser,
                $request->id_properti, 
                $request->checkin,
                $request->checkout,
                $request->metode_bayar
            ]);

            $response = $result[0] ?? null;
            $messageRaw = $response->message ?? 'Error: No Response from Database';

            if (str_starts_with($messageRaw, 'ERROR')) {
                 $cleanMsg = str_replace('ERROR: ', '', $messageRaw);
                 return back()->with('error', 'Database Menolak: ' . $cleanMsg)->withInput();
            }

            return redirect()->route('customer.dashboard')->with('success', 'Booking Successful! Please proceed to payment.');

        } catch (\Exception $e) {
            return back()->with('error', 'SQL ERROR: ' . $e->getMessage())->withInput();
        }
    }

    public function cancel(Request $request, $id){

    $request->validate([
        'alasan' => 'required|string|max:255'
    ]);

    try {
        $result = DB::select("CALL cancel_booking(?, ?)", [
            $id,
            $request->alasan
        ]);

        $response = $result[0] ?? null;
        if (!$response) {
            return back()->with('error', 'Gagal: Tidak ada respons dari database');
        }

        $message = $response->message;

        if (str_starts_with($message, 'ERROR')) {
            $cleanMessage = str_replace('ERROR: ', '', $message);
            return back()->with('error', $cleanMessage);
        }

        $cleanMessage = str_replace('SUCCESS: ', '', $message);
        return back()->with('success', $cleanMessage);

    } catch (\Illuminate\Database\QueryException $e) {
        return back()->with('error', 'Database Error: ' . $e->getMessage());
        
    } catch (\Exception $e) {
        return back()->with('error', 'Sistem Error: ' . $e->getMessage());
    }
}
}