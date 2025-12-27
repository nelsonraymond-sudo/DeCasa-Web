<?php

use Illuminate\Support\Facades\Route;
// Admin Controllers
use App\Http\Controllers\Admin\DashboardController; 
use App\Http\Controllers\Admin\PropertiController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\FasilitasController;
use App\Http\Controllers\Admin\TransaksiController;
use App\Http\Controllers\Admin\CustomerController; 
use App\Http\Controllers\Admin\SettingController;
// Auth & Customer Controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Customer\LandingController;
use App\Http\Controllers\Customer\TransactionController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboard;

Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/search', [LandingController::class, 'search'])->name('properti.search');

// PERHATIKAN INI: Route parameter kita namakan {id_properti} biar jelas
Route::get('/properti/{id_properti}', [LandingController::class, 'show'])->name('properti.detail');


// --- AUTHENTICATION (Login/Register) ---
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'processLogin'])->name('login.process');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'processRegister'])->name('register.process');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    
    // Dashboard History
   Route::get('/dashboard', [CustomerDashboard::class, 'index'])->name('customer.dashboard');

   Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/logout', [ProfileController::class, 'logout'])->name('logout');

    Route::get('/booking/{id}', [TransactionController::class, 'showBookingForm'])
         ->name('booking.form');

    // Proses Booking
    Route::post('/booking/process', [TransactionController::class, 'store'])->name('booking.process');

    // Cancel Booking (Method PUT)
    // Parameter {id} akan ditangkap oleh $id di controller
   Route::put('/transaksi/{id}/cancel', [TransactionController::class, 'cancel'])
         ->name('customer.transaksi.cancel');
});



// --- 3. HALAMAN ADMIN (PROTECTED) ---
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

    // Fasilitas
    Route::get('/fasilitas', [FasilitasController::class, 'index'])->name('fasilitas.index');
    Route::post('/fasilitas', [FasilitasController::class, 'store'])->name('fasilitas.store');
    Route::put('/fasilitas/{id}', [FasilitasController::class, 'update'])->name('fasilitas.update');
    Route::delete('/fasilitas/{id}', [FasilitasController::class, 'destroy'])->name('fasilitas.destroy');

    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::put('/transaksi/{id}/confirm', [TransaksiController::class, 'confirm'])->name('transaksi.confirm');
    Route::delete('/transaksi/{id}/cancel', [TransaksiController::class, 'destroy'])->name('transaksi.destroy');

    // Customer & Setting
    Route::get('/customer', [CustomerController::class, 'index'])->name('customer.index');
    Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
    Route::put('/setting', [SettingController::class, 'update'])->name('setting.update');
});