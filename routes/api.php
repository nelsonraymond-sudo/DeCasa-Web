<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PropertiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'loginApi']);
Route::post('/register', [AuthController::class, 'registerApi']);

Route::get('/properti', [PropertiController::class, 'index']);
Route::get('/properti/search', [PropertiController::class, 'search']);
Route::get('/properti/{id}', [PropertiController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/customer/profile', [CustomerController::class, 'show']);
    Route::post('/customer/profile/update', [CustomerController::class, 'update']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::post('/transactions/preview', [TransactionController::class, 'preview']);
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);
    Route::get('/booked-dates/{id}', [TransactionController::class, 'getBookedDates']);

    Route::get('/payment-methods', [PaymentController::class, 'index']);
});
