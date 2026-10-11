<?php

use App\Http\Controllers\Api\V1\AdminPenggunaController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\FasilitasController;
use App\Http\Controllers\Api\V1\KamarController;
use App\Http\Controllers\Api\V1\KosController;
use App\Http\Controllers\Api\V1\PemesananController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    // ------------------------------------------------------------------------
    // Public Endpoints
    // ------------------------------------------------------------------------
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/kos', [KosController::class, 'index']);
    Route::get('/kos/{id}', [KosController::class, 'show']);
    Route::get('/kos/{id}/kamar', [KamarController::class, 'index']);

    Route::get('/fasilitas', [FasilitasController::class, 'index']);

    // ------------------------------------------------------------------------
    // Authenticated Endpoints (Sanctum)
    // ------------------------------------------------------------------------
    Route::middleware('auth:sanctum')->group(function (): void {
        // Profil Akun
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Manajemen Kos (Pemilik & Admin)
        Route::post('/kos', [KosController::class, 'store'])->middleware('role:pemilik');
        Route::put('/kos/{id}', [KosController::class, 'update'])->middleware('role:pemilik,admin');
        Route::delete('/kos/{id}', [KosController::class, 'destroy'])->middleware('role:pemilik,admin');

        // Manajemen Kamar (Pemilik & Admin)
        Route::post('/kos/{id}/kamar', [KamarController::class, 'store'])->middleware('role:pemilik');
        Route::put('/kamar/{id}', [KamarController::class, 'update'])->middleware('role:pemilik,admin');
        Route::delete('/kamar/{id}', [KamarController::class, 'destroy'])->middleware('role:pemilik,admin');

        // Alur Pemesanan
        Route::post('/pemesanan', [PemesananController::class, 'store'])->middleware('role:pencari');
        Route::get('/pemesanan', [PemesananController::class, 'index']);
        Route::get('/pemesanan/{id}', [PemesananController::class, 'show']);
        Route::patch('/pemesanan/{id}/status', [PemesananController::class, 'updateStatus']);

        // Master Fasilitas (Admin Only)
        Route::middleware('role:admin')->group(function (): void {
            Route::post('/fasilitas', [FasilitasController::class, 'store']);
            Route::put('/fasilitas/{id}', [FasilitasController::class, 'update']);
            Route::delete('/fasilitas/{id}', [FasilitasController::class, 'destroy']);

            // Manajemen Pengguna (Admin Only)
            Route::get('/admin/pengguna', [AdminPenggunaController::class, 'index']);
            Route::patch('/admin/pengguna/{id}/status', [AdminPenggunaController::class, 'updateStatus']);
        });
    });
});
