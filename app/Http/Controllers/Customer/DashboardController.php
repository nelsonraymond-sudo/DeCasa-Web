<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\Transaksi;            

class DashboardController extends Controller
{
    public function index()
    {
        $id_user = Auth::id();
        $transactions = Transaksi::with('properti') 
                        ->where('id_user', $id_user)
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('customer.dashboard', compact('transactions'));
    }
}