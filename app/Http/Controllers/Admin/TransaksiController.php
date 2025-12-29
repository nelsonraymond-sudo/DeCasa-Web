<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi; 

class TransaksiController extends Controller
{
    public function index()
    {
        $transactions = Transaksi::with(['user', 'properti'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('admin.transaksi.index', compact('transactions'));
    }

    public function confirm($id)
    {
        $transaction = Transaksi::findOrFail($id);
        
        $transaction->status = 'lunas';
        $transaction->save();

        return back()->with('success', 'Payment successfully confirmed!');
    }
    
    public function destroy($id)
    {
        $transaction = Transaksi::findOrFail($id);
        $transaction->status = 'batal';
        $transaction->save();
        
        return back()->with('success', 'The transaction has been canceled.');
    }
}