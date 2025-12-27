<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi; 

class TransaksiController extends Controller
{
    // Menampilkan daftar semua transaksi
    public function index()
    {
        // Ambil data transaksi beserta User dan Properti-nya
        // Urutkan dari yang terbaru
        $transactions = Transaksi::with(['user', 'properti'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('admin.transaksi.index', compact('transactions'));
    }

    // Mengubah status menjadi LUNAS
    public function confirm($id)
    {
        $transaction = Transaksi::findOrFail($id);
        
        // Update status manual
        $transaction->status = 'lunas';
        $transaction->save();

        return back()->with('success', 'Payment successfully confirmed!');
    }
    
    // (Opsional) Jika ingin membatalkan transaksi dari sisi admin
    public function destroy($id)
    {
        $transaction = Transaksi::findOrFail($id);
        $transaction->status = 'batal';
        $transaction->save();
        
        return back()->with('success', 'The transaction has been canceled.');
    }
}