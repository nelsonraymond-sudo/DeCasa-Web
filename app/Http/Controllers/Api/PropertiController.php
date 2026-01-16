<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Properti; 
use Illuminate\Http\Request;

class PropertiController extends Controller
{
    // 1. Ambil Semua Data Properti
    public function index()
    {
        $properti = Properti::with(['foto', 'fasilitas'])->get(); 

        return response()->json([
            'success' => true,
            'message' => 'Daftar Properti',
            'data'    => $properti
        ], 200);
    }

    // 2. Ambil Detail 1 Properti
    public function show($id)
    {
        $properti = Properti::find($id);

        if (!$properti) {
            return response()->json([
                'success' => false,
                'message' => 'Properti tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail Properti',
            'data'    => $properti
        ], 200);
    }
}