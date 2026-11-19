<?php

use App\Http\Controllers\AcademicoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ReservacionController;
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

    // Práctica 1.4 - Gestión de Productos (Administrador y Empleado: consulta)
    Route::middleware('role:administrador,empleado')->group(function () {
        Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
    });

    // Práctica 1.4 - CRUD de Productos (Solo Administrador)
    Route::middleware('role:administrador')->group(function () {
        Route::get('/productos/crear', [ProductoController::class, 'create'])->name('productos.create');
        Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
        Route::get('/productos/{producto}/editar', [ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
        Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');
    });

    // Práctica 1.5 - Reservación de Equipos (Administrador y Estudiante)
    Route::middleware('role:administrador,estudiante')->group(function () {
        Route::get('/equipos', [EquipoController::class, 'index'])->name('equipos.index');
        Route::get('/reservaciones', [ReservacionController::class, 'index'])->name('reservaciones.index');
        Route::post('/reservaciones', [ReservacionController::class, 'store'])->name('reservaciones.store');
        Route::get('/reservaciones/{reservacion}', [ReservacionController::class, 'show'])->name('reservaciones.show');
        Route::patch('/reservaciones/{reservacion}/cancelar', [ReservacionController::class, 'cancelar'])->name('reservaciones.cancelar');
    });

    // Práctica 1.5 - CRUD de Equipos de Cómputo (Solo Administrador)
    Route::middleware('role:administrador')->group(function () {
        Route::get('/equipos/crear', [EquipoController::class, 'create'])->name('equipos.create');
        Route::post('/equipos', [EquipoController::class, 'store'])->name('equipos.store');
        Route::get('/equipos/{equipo}/editar', [EquipoController::class, 'edit'])->name('equipos.edit');
        Route::put('/equipos/{equipo}', [EquipoController::class, 'update'])->name('equipos.update');
        Route::delete('/equipos/{equipo}', [EquipoController::class, 'destroy'])->name('equipos.destroy');
    });
});
