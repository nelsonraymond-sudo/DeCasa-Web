<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        
        $laporan = DB::table('laporan_decasa')->first();

        $totalProperti    = $laporan->total_properti ?? 0;
        $propertiTersedia = $laporan->properti_tersedia ?? 0;
        $propertiTerisi   = $laporan->properti_penuh ?? 0;
        
        $totalPendapatan  = $laporan->pendapatan_bulan_ini ?? 0; 
        
        return view('admin.dashboard', compact(
            'totalProperti',
            'propertiTersedia',
            'propertiTerisi',
            'totalPendapatan'
        ));
    }
}