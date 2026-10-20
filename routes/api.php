<?php

use App\Http\Controllers\Api\V1\AdminKosController;
use App\Http\Controllers\Api\V1\AdminUserController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\FacilityController;
use App\Http\Controllers\Api\V1\KosController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\RoomController;
use App\Http\Controllers\Api\V1\UploadController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function (): void {
    // ------------------------------------------------------------------------
    // Public Endpoints
    // ------------------------------------------------------------------------
    Route::prefix('auth')->name('auth.')->group(function (): void {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
    });
    // Legacy aliases
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Kos (Properties)
    Route::get('/kos', [KosController::class, 'index'])->name('kos.index');
    Route::get('/kos/{id}', [KosController::class, 'show'])->name('kos.show');
    Route::get('/kos/{id}/rooms', [RoomController::class, 'index'])->name('kos.rooms.index');
    Route::get('/kos/{id}/reviews', [ReviewController::class, 'index'])->name('kos.reviews.index');
    // Legacy aliases
    Route::get('/kos/{id}/kamar', [RoomController::class, 'index']);
    Route::get('/kos/{id}/ulasan', [ReviewController::class, 'index']);

    // Facilities
    Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
    Route::get('/fasilitas', [FacilityController::class, 'index']); // Legacy alias

    // ------------------------------------------------------------------------
    // Authenticated Endpoints (Sanctum)
    // ------------------------------------------------------------------------
    Route::middleware('auth:sanctum')->group(function (): void {
        // Auth & Profile
        Route::prefix('auth')->name('auth.')->group(function (): void {
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('/me', [AuthController::class, 'me'])->name('me');
        });
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me'])->name('profile.show');
        Route::put('/me', [AuthController::class, 'updateProfile'])->name('profile.update');
        Route::post('/me/avatar', [AuthController::class, 'updateAvatar'])->name('profile.avatar');
        Route::put('/me/password', [AuthController::class, 'updatePassword'])->name('profile.password');

        // Media / Image Upload
        Route::post('/uploads/images', [UploadController::class, 'uploadImages'])->middleware('role:pemilik,admin')->name('uploads.images');
        Route::post('/upload/images', [UploadController::class, 'uploadImages'])->middleware('role:pemilik,admin'); // Legacy alias

        // Manajemen Kos (Pemilik & Admin)
        Route::post('/kos', [KosController::class, 'store'])->middleware('role:pemilik')->name('kos.store');
        Route::put('/kos/{id}', [KosController::class, 'update'])->middleware('role:pemilik,admin')->name('kos.update');
        Route::delete('/kos/{id}', [KosController::class, 'destroy'])->middleware('role:pemilik,admin')->name('kos.destroy');

        // Rooms Management (Pemilik & Admin)
        Route::post('/kos/{id}/rooms', [RoomController::class, 'store'])->middleware('role:pemilik')->name('kos.rooms.store');
        Route::put('/rooms/{id}', [RoomController::class, 'update'])->middleware('role:pemilik,admin')->name('rooms.update');
        Route::delete('/rooms/{id}', [RoomController::class, 'destroy'])->middleware('role:pemilik,admin')->name('rooms.destroy');
        // Legacy aliases
        Route::post('/kos/{id}/kamar', [RoomController::class, 'store'])->middleware('role:pemilik');
        Route::put('/kamar/{id}', [RoomController::class, 'update'])->middleware('role:pemilik,admin');
        Route::delete('/kamar/{id}', [RoomController::class, 'destroy'])->middleware('role:pemilik,admin');

        // Bookings Flow
        Route::post('/bookings', [BookingController::class, 'store'])->middleware('role:pencari')->name('bookings.store');
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');
        Route::patch('/bookings/{id}/status', [BookingController::class, 'updateStatus'])->name('bookings.update-status');
        // Legacy aliases
        Route::post('/pemesanan', [BookingController::class, 'store'])->middleware('role:pencari');
        Route::get('/pemesanan', [BookingController::class, 'index']);
        Route::get('/pemesanan/{id}', [BookingController::class, 'show']);
        Route::patch('/pemesanan/{id}/status', [BookingController::class, 'updateStatus']);

        // Reviews & Rating
        Route::get('/kos/{id}/reviews/eligibility', [ReviewController::class, 'eligibility'])->name('kos.reviews.eligibility');
        Route::post('/kos/{id}/reviews', [ReviewController::class, 'store'])->middleware('role:pencari')->name('kos.reviews.store');
        // Legacy aliases
        Route::get('/kos/{id}/ulasan/eligibility', [ReviewController::class, 'eligibility']);
        Route::post('/kos/{id}/ulasan', [ReviewController::class, 'store'])->middleware('role:pencari');

        // Facilities Management (Pemilik & Admin)
        Route::post('/facilities', [FacilityController::class, 'store'])->middleware('role:pemilik,admin')->name('facilities.store');
        Route::post('/fasilitas', [FacilityController::class, 'store'])->middleware('role:pemilik,admin'); // Legacy alias

        // Admin Only Routes
        Route::middleware('role:admin')->group(function (): void {
            Route::put('/facilities/{id}', [FacilityController::class, 'update'])->name('facilities.update');
            Route::delete('/facilities/{id}', [FacilityController::class, 'destroy'])->name('facilities.destroy');
            Route::put('/fasilitas/{id}', [FacilityController::class, 'update']); // Legacy alias
            Route::delete('/fasilitas/{id}', [FacilityController::class, 'destroy']); // Legacy alias

            // User Management & Statistics
            Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
            Route::get('/admin/users/{id}', [AdminUserController::class, 'show'])->name('admin.users.show');
            Route::patch('/admin/users/{id}/status', [AdminUserController::class, 'updateStatus'])->name('admin.users.update-status');
            // Legacy aliases
            Route::get('/admin/pengguna', [AdminUserController::class, 'index']);
            Route::get('/admin/pengguna/{id}', [AdminUserController::class, 'show']);
            Route::patch('/admin/pengguna/{id}/status', [AdminUserController::class, 'updateStatus']);

            Route::get('/admin/stats', [AdminUserController::class, 'stats'])->name('admin.stats');

            // Kos Moderation
            Route::get('/admin/kos', [AdminKosController::class, 'index'])->name('admin.kos.index');
            Route::delete('/admin/kos/{id}', [AdminKosController::class, 'destroy'])->name('admin.kos.destroy');
        });
    });
});
