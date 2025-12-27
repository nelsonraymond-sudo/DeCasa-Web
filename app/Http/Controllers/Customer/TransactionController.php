<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    // 1. DASHBOARD HISTORY
    public function index()
    {
        $userId = Auth::id();
        
        // FIX 1: Samakan nama variable dengan DashboardController ($transaksi)
        // Agar tidak error "Undefined variable: transaksi" di view
        $transaksi = DB::table('view_booking_history') 
            ->where('id_user', $userId)
            ->orderBy('tanggal_book', 'desc')
            ->get();

        return view('customer.dashboard', compact('transaksi'));
    }

    // 2. MENAMPILKAN FORM BOOKING
    public function showBookingForm($id)
    {
        $properti = DB::table('view_detail_properti')->where('id_properti', $id)->first();
        $payment = DB::table('payment')->get(); 

        if (!$properti) {
            return redirect()->back()->with('error', 'Properti tidak ditemukan');
        }

        return view('customer.booking', compact('properti', 'payment'));
    }

    // 3. PROSES SIMPAN BOOKING (POST)
   

    public function store(Request $request)
    {
        // Validasi Input
        $request->validate([
            'id_properti' => 'required',
            'checkin'     => 'required|date',
            'checkout'    => 'required|date|after:checkin',
            'metode_bayar'=> 'required'
        ]);

        try {
            $idUser = Auth::id();
            
            // Debugging: Cek data yang dikirim sebelum masuk SQL
            // Jika ingin melihat data, uncomment baris bawah ini:
            // dd($idUser, $request->all());

            // Panggil Procedure
            // Pastikan jumlah tanda tanya (?) SAMA dengan parameter di MySQL Anda
            $result = DB::select("CALL create_booking(?, ?, ?, ?, ?)", [
                $idUser,
                $request->id_properti, 
                $request->checkin,
                $request->checkout,
                $request->metode_bayar
            ]);

            // Ambil hasil response dari Procedure
            $response = $result[0] ?? null;
            $messageRaw = $response->message ?? 'Error: No Response from Database';

            // Cek status dari Procedure (Success / Error)
            if (str_starts_with($messageRaw, 'ERROR')) {
                 $cleanMsg = str_replace('ERROR: ', '', $messageRaw);
                 return back()->with('error', 'Database Menolak: ' . $cleanMsg)->withInput();
            }

            return redirect()->route('customer.dashboard')->with('success', 'Booking Berhasil! Silakan lakukan pembayaran.');

        } catch (\Exception $e) {
            // INI BAGIAN PENTING:
            // Kita akan memunculkan pesan error asli dari SQL ke layar
            // agar ketahuan apa penyebabnya (misal: Function missing, Column unknown, dll)
            return back()->with('error', 'SQL ERROR: ' . $e->getMessage())->withInput();
        }
    }

    // 4. CANCEL BOOKING (PUT)
    public function cancel(Request $request, $id)
{
    $request->validate([
        'alasan' => 'required|string|max:255'
    ]);

    try {
        // Panggil procedure
        $result = DB::select("CALL cancel_booking(?, ?)", [
            $id,
            $request->alasan
        ]);

        // Ambil respon dari procedure
        $response = $result[0] ?? null;
        
        // Default message jika procedure tidak me-return apa-apa (jarang terjadi)
        if (!$response) {
            return back()->with('error', 'Gagal: Tidak ada respons dari database');
        }

        $message = $response->message;

        // Cek apakah procedure mengembalikan pesan ERROR buatan kita
        if (str_starts_with($message, 'ERROR')) {
            $cleanMessage = str_replace('ERROR: ', '', $message);
            return back()->with('error', $cleanMessage);
        }

        // Jika Sukses
        $cleanMessage = str_replace('SUCCESS: ', '', $message);
        return back()->with('success', $cleanMessage);

    } catch (\Illuminate\Database\QueryException $e) {
        // PERBAIKAN: Tangkap error SQL spesifik
        // Jika masih ada error collation atau syntax, akan muncul detailnya di sini
        return back()->with('error', 'Database Error: ' . $e->getMessage());
        
    } catch (\Exception $e) {
        // Tangkap error umum lainnya
        return back()->with('error', 'Sistem Error: ' . $e->getMessage());
    }
}
}