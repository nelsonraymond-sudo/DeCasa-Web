<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
   public function index()
{
    $totalUnit = DB::table('properti')->count();

    $available = DB::table('properti')
                ->where('status', 'available') 
                ->count();
                
    $occupied = DB::table('properti')
                ->where('status', 'full')
                ->count();

    $revenue = DB::table('view_laporan_decasa')->sum('total_revenue');

    return view('admin.dashboard', compact('totalUnit', 'available', 'occupied', 'revenue'));
}
}