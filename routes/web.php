<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Rutas públicas (solo invitados)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Rutas de verificación OTP (3 etapas para Administrador)
Route::get('/verificacion', [AuthController::class, 'showOtpForm'])
    ->middleware('signed')
    ->name('otp.form');

Route::post('/verificacion', [AuthController::class, 'verifyOtp'])
    ->name('otp.verify');

// Rutas protegidas (usuarios autenticados)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
