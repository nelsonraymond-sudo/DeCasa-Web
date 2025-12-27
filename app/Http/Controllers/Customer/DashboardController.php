<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Wajib import Auth untuk tahu siapa yang login
use App\Models\Transaksi;            // Wajib import Model Transaksi

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil ID User yang sedang login
        $id_user = Auth::id();

        // 2. Ambil data transaksi milik user tersebut
        // Kita pakai 'with' untuk mengambil data properti sekaligus (biar nama properti muncul)
        $transactions = Transaksi::with('properti') 
                        ->where('id_user', $id_user)
                        ->orderBy('created_at', 'desc') // Urutkan dari yang terbaru
                        ->get();

        // 3. Kirim variabel $transactions ke View
        // 'compact' adalah cara cepat mengirim variabel ke blade
        return view('customer.dashboard', compact('transactions'));
    }
}