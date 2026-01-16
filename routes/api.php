<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerApiController;

// Route Public (Tidak butuh token)
Route::post('/login', [CustomerApiController::class, 'login']);
Route::get('/properti', [CustomerApiController::class, 'getProperti']); // Jika mau bisa dilihat tanpa login

// Route Private (Butuh Token - User harus login dulu)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/booking', [CustomerApiController::class, 'booking']);
    Route::post('/logout', [CustomerApiController::class, 'logout']);
    
    // Cek user yang sedang login
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});