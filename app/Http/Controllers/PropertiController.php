<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Hitung-hitungan Properti (Masih manual karena View Anda memfilter status)
        $totalProperti    = DB::table('properti')->count();
        
        // Bisa pakai view_properti_tersedia untuk hitung yang tersedia
        $propertiTersedia = DB::table('view_properti_tersedia')->count(); 
        
        $propertiTerisi   = $totalProperti - $propertiTersedia;

        // 2. Total Pendapatan dari VIEW_LAPORAN_DECASA
        // Kolom di view Anda adalah 'total_revenue'
        $totalPendapatan = DB::table('view_laporan_decasa')->sum('total_revenue');

        return view('admin.dashboard', compact(
            'totalProperti', 
            'propertiTersedia', 
            'propertiTerisi',
            'totalPendapatan'
        ));
        
    }
}