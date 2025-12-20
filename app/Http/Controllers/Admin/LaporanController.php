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

            $laporan = DB::table('transaksi') 
                ->join('users', 'transaksi.id_user', '=', 'users.id_user') 
                ->join('properti', 'transaksi.id_properti', '=', 'properti.id_properti') 
                ->select(
                    'transaksi.*', 
                    'users.nm_user', 
                    'properti.nm_properti'
                )
                ->orderBy('transaksi.tgl_trans', 'desc') 
                ->get();

            $totalPemasukan = $laporan->where('status', 'selesai')->sum('total_harga');

        } catch (\Exception $e) {
            $laporan = [];
            $totalPemasukan = 0;
            session()->flash('error', 'Gagal mengambil data laporan: ' . $e->getMessage());
        }

        return view('admin.laporan.index', compact('laporan', 'totalPemasukan'));
    }
}