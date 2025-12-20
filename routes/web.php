<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController; 
use App\Http\Controllers\Admin\PropertiController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome'); 
});
// Authentication Routes
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'processLogin'])->name('login.process');
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'processRegister'])->name('register.process');
});
// Logout 
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard 
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Properti
    Route::get('/properti', [PropertiController::class, 'index'])->name('properti.index');
    Route::get('/properti/manage', [PropertiController::class, 'manage'])->name('properti.manage');
    Route::get('/properti/create', [PropertiController::class, 'create'])->name('properti.create');
    Route::post('/properti', [PropertiController::class, 'store'])->name('properti.store');
    
    Route::get('/properti/{id}/detail', [PropertiController::class, 'show'])->name('properti.show');
    Route::get('/properti/{id}/edit', [PropertiController::class, 'edit'])->name('properti.edit');
    Route::put('/properti/{id}', [PropertiController::class, 'update'])->name('properti.update');
    Route::delete('/properti/{id}', [PropertiController::class, 'destroy'])->name('properti.destroy');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
});