<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Properti;
use Illuminate\Http\Request;

class PropertiController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $properti = Properti::with(['foto', 'fasilitas.fasilitas', 'kategori'])->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar Properti',
            'data' => $properti
        ], 200);
    }

    /**
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $properti = Properti::with(['foto', 'fasilitas.fasilitas', 'kategori'])->find($id);

        if (!$properti) {
            return response()->json([
                'success' => false,
                'message' => 'Properti tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail Properti',
            'data' => $properti
        ], 200);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = Properti::query()->with(['foto', 'fasilitas.fasilitas', 'kategori']);

        if ($request->has('nama')) {
            $query->where('nm_properti', 'like', '%' . $request->nama . '%');
        }

        if ($request->has('lokasi')) {
            $query->where('alamat', 'like', '%' . $request->lokasi . '%');
        }

        if ($request->has('harga_min')) {
            $query->where('harga', '>=', $request->harga_min);
        }

        if ($request->has('harga_max')) {
            $query->where('harga', '<=', $request->harga_max);
        }

        if ($request->has('kategori')) {
            $query->where('id_kategori', $request->kategori);
        }

        $properti = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Hasil Pencarian',
            'data' => $properti
        ], 200);
    }
}