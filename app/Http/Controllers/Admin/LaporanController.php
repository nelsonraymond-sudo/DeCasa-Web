<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        try {
            $laporan = DB::table('view_booking_history')
                ->orderBy('tanggal_book', 'desc') 
                ->get();
                
            $totalPemasukan = $laporan->where('status', 'selesai')->sum('total_harga');

        } catch (\Exception $e) {
            $laporan = collect([]); 
            $totalPemasukan = 0;
            session()->flash('error', 'Failed to retrieve report data: ' . $e->getMessage());
        }

        return view('admin.laporan.index', compact('laporan', 'totalPemasukan'));
    }
}