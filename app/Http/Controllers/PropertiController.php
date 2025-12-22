<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProperti    = DB::table('properti')->count();
        
        $propertiTersedia = DB::table('view_properti_tersedia')->count(); 
        
        $propertiTerisi   = $totalProperti - $propertiTersedia;

        $totalPendapatan = DB::table('view_laporan_decasa')->sum('total_revenue');

        return view('admin.dashboard', compact(
            'totalProperti', 
            'propertiTersedia', 
            'propertiTerisi',
            'totalPendapatan'
        ));
        
    }
}