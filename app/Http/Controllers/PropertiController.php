<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{ public function index()
    {
        $properti = DB::table('view_properti_tersedia')->get();

        return view('admin.properti.index', compact('properti'));
    }
public function dashboard()
{
    $totalProperti = DB::table('properti')->count();
    $propertiTersedia = DB::table('properti')->where('status', 'tersedia')->count();
    $propertiTerisi = DB::table('properti')->where('status', '!=', 'tersedia')->count();
    $totalPendapatan = DB::table('laporan_decasa')->sum('total_harga') ?? 0;

    return view('admin.dashboard', compact(
        'totalProperti', 
        'propertiTersedia', 
        'propertiTerisi',
        'totalPendapatan'
    ));
}
    public function show($id)
    {
        $properti = \Illuminate\Support\Facades\DB::table('properti')
            ->where('id_properti', $id)
            ->first();

        if (!$properti) {
            return redirect()->back()->with('error', 'Properti tidak ditemukan');
        }

        $fasilitas = \Illuminate\Support\Facades\DB::table('detailfasilitas')
            ->join('fasilitas', 'detailfasilitas.id_fasilitas', '=', 'fasilitas.id_fasilitas')
            ->where('detailfasilitas.id_properti', $id)
            ->get();

        return view('admin.properti.show', compact('properti', 'fasilitas'));
    }
    
}
    