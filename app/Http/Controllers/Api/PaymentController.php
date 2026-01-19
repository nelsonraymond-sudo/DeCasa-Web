<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of available payment methods.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $payments = Payment::all();

        return response()->json([
            'success' => true,
            'message' => 'Daftar Metode Pembayaran',
            'data' => $payments
        ], 200);
    }
}
