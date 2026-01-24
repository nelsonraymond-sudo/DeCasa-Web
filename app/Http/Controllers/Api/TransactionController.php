<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Properti;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a list of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $transactions = Transaksi::where('id_user', $user->id_user)
            ->with(['properti', 'properti.foto'])
            ->orderBy('tgl_trans', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'List Transaksi User',
            'data' => $transactions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // 1. HIDARI VALIDASI 'exists' agar bisa mencari berdasarkan Nama Bank juga
        $validator = Validator::make($request->all(), [
            'id_properti' => 'required|exists:properti,id_properti',
            'id_metode' => 'required', // Wajib diisi, tapi pencarian dilakukan di bawah
            'checkin' => 'required|date|after_or_equal:today',
            'checkout' => 'required|date|after:checkin',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $id_metode_input = trim($request->id_metode);
        $payment = \App\Models\Payment::where('id_metode', $id_metode_input)->first();

        if (!$payment) {
            $payment = \App\Models\Payment::where('nama_bank', 'like', '%' . $id_metode_input . '%')->first();
        }

        if (!$payment) {
            $payment = \App\Models\Payment::first();
        }

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Metode pembayaran tidak valid: ' . $id_metode_input
            ], 422);
        }

        try {
            $id_metode = $payment->id_metode;

            $checkin = Carbon::parse($request->checkin);
            $checkout = Carbon::parse($request->checkout);
            $durasi = $checkin->diffInDays($checkout);

            if ($durasi < 1)
                $durasi = 1;

            $properti = Properti::find($request->id_properti);
            $total_harga = (string) ($properti->harga * $durasi);

            $dateCode = date('ymd');
            $prefix = 'TRX' . $dateCode . '-';

            $lastTrans = Transaksi::where('id_trans', 'like', $prefix . '%')
                ->orderBy('id_trans', 'desc')
                ->first();

            if ($lastTrans) {
                $lastNo = (int) substr($lastTrans->id_trans, -3);
                $nextNo = $lastNo + 1;
            } else {
                $nextNo = 1;
            }

            $id_trans = $prefix . str_pad($nextNo, 3, '0', STR_PAD_LEFT);

            $transaksi = Transaksi::create([
                'id_trans' => $id_trans,
                'id_user' => $user->id_user,
                'id_properti' => $request->id_properti,
                'id_metode' => $id_metode, 
                'tgl_trans' => Carbon::now(),
                'checkin' => $checkin->toDateString(), 
                'checkout' => $checkout->toDateString(), 
                'durasi' => $durasi,
                'total_harga' => $total_harga,
                'status' => 'pending'
            ]);

            $transaksi->load(['properti.foto', 'properti.fasilitas.fasilitas', 'properti.kategori', 'payment']);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi Berhasil Dibuat',
                'data' => $transaksi
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview booking details before recording to database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_properti' => 'required|exists:properti,id_properti',
            'checkin' => 'required|date|after_or_equal:today',
            'checkout' => 'required|date|after:checkin',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $checkin = Carbon::parse($request->checkin);
        $checkout = Carbon::parse($request->checkout);
        $durasi = $checkin->diffInDays($checkout);
        if ($durasi < 1)
            $durasi = 1;

        $properti = Properti::with(['foto', 'fasilitas.fasilitas', 'kategori'])->find($request->id_properti);
        if (!$properti) {
            return response()->json([
                'success' => false,
                'message' => 'Properti tidak ditemukan (ID: ' . $request->id_properti . ')'
            ], 404);
        }
        $amount = (string) ($properti->harga * $durasi);
        $tax = "0";
        $total = $amount;

        return response()->json([
            'success' => true,
            'message' => 'Review Summary Booking',
            'data' => [
                'properti' => $properti,
                'checkin' => $request->checkin,
                'checkout' => $request->checkout,
                'durasi' => $durasi,
                'amount' => $amount,
                'tax' => $tax,
                'total' => $total
            ]
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = Auth::user();
        $transaksi = Transaksi::with(['properti', 'properti.foto', 'payment'])
            ->where('id_trans', $id)
            ->where('id_user', $user->id_user)
            ->first();

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Detail Transaksi',
            'data' => $transaksi
        ]);
    }
    /**
     * Get booked dates for a property.
     *
     * @param  string  $id_properti
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBookedDates($id_properti)
    {
        $transactions = Transaksi::where('id_properti', $id_properti)
            ->whereIn('status', ['pending', 'lunas'])
            ->get(['checkin', 'checkout']);

        return response()->json([
            'success' => true,
            'message' => 'Booked Dates',
            'data' => $transactions
        ]);
    }
}
