<?php

use App\Http\Controllers\AcademicoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
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

    // Práctica 1.2 - Gestión de Usuarios (Administrador y Empleado)
    Route::middleware('role:administrador,empleado')->group(function () {
        Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/crear', [UserController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/{usuario}', [UserController::class, 'show'])->name('usuarios.show');
        Route::get('/usuarios/{usuario}/editar', [UserController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{usuario}', [UserController::class, 'update'])->name('usuarios.update');
    });

    // Práctica 1.2 - Eliminación lógica, papelera, restauración y borrado permanente (Solo Administrador)
    Route::middleware('role:administrador')->group(function () {
        Route::get('/usuarios-papelera', [UserController::class, 'trashed'])->name('usuarios.trashed');
        Route::delete('/usuarios/{usuario}', [UserController::class, 'destroy'])->name('usuarios.destroy');
        Route::patch('/usuarios/{id}/restaurar', [UserController::class, 'restore'])->name('usuarios.restore');
        Route::delete('/usuarios/{id}/permanente', [UserController::class, 'forceDelete'])->name('usuarios.forceDelete');
    });

    // Práctica 1.3 - Sistema Académico (Profesor y Alumno)
    Route::middleware('role:profesor,alumno')->group(function () {
        Route::get('/academico', [AcademicoController::class, 'index'])->name('academico.index');
    });

    // Práctica 1.3 - Gestión de registros académicos (Solo Profesor)
    Route::middleware('role:profesor')->group(function () {
        Route::get('/academico/crear', [AcademicoController::class, 'create'])->name('academico.create');
        Route::post('/academico', [AcademicoController::class, 'store'])->name('academico.store');
        Route::get('/academico/{registro}/editar', [AcademicoController::class, 'edit'])->name('academico.edit');
        Route::put('/academico/{registro}', [AcademicoController::class, 'update'])->name('academico.update');
        Route::delete('/academico/{registro}', [AcademicoController::class, 'destroy'])->name('academico.destroy');
    });
});
