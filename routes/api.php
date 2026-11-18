<?php

use App\Http\Controllers\Api\V1\AdminKosController;
use App\Http\Controllers\Api\V1\AdminUserController;
use App\Http\Controllers\Api\V1\AdminWithdrawalController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\FacilityController;
use App\Http\Controllers\Api\V1\HeroBannerController;
use App\Http\Controllers\Api\V1\KosController;
use App\Http\Controllers\Api\V1\MidtransWebhookController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\PropertyVerificationController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\RoomController;
use App\Http\Controllers\Api\V1\UploadController;
use App\Http\Controllers\Api\V1\WalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function (): void {
    // ------------------------------------------------------------------------
    // Public Endpoints
    // ------------------------------------------------------------------------
    Route::prefix('auth')->name('auth.')->group(function (): void {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
    });
    // Legacy aliases
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

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

    // Hero Banners (Homepage Slider)
    Route::get('/hero-banners', [HeroBannerController::class, 'publicIndex'])->name('hero-banners.index');

    // Midtrans Payment Webhook
    Route::post('/webhooks/midtrans', [MidtransWebhookController::class, 'handle'])->name('webhooks.midtrans');

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

        // Property Ownership Verification (Pemilik)
        Route::get('/pemilik/verification', [PropertyVerificationController::class, 'show'])->middleware('role:pemilik')->name('pemilik.verification.show');
        Route::post('/pemilik/verification', [PropertyVerificationController::class, 'submit'])->middleware('role:pemilik')->name('pemilik.verification.submit');
        Route::post('/pemilik/verification/upload', [PropertyVerificationController::class, 'uploadDocument'])->middleware('role:pemilik')->name('pemilik.verification.upload');

        // Manajemen Kos (Pemilik & Admin - Pemilik wajib terverifikasi)
        Route::post('/kos', [KosController::class, 'store'])->middleware(['role:pemilik', 'verified_owner'])->name('kos.store');
        Route::put('/kos/{id}', [KosController::class, 'update'])->middleware(['role:pemilik,admin', 'verified_owner'])->name('kos.update');
        Route::delete('/kos/{id}', [KosController::class, 'destroy'])->middleware(['role:pemilik,admin', 'verified_owner'])->name('kos.destroy');

        // Rooms Management (Pemilik & Admin - Pemilik wajib terverifikasi)
        Route::post('/kos/{id}/rooms', [RoomController::class, 'store'])->middleware(['role:pemilik', 'verified_owner'])->name('kos.rooms.store');
        Route::put('/rooms/{id}', [RoomController::class, 'update'])->middleware(['role:pemilik,admin', 'verified_owner'])->name('rooms.update');
        Route::delete('/rooms/{id}', [RoomController::class, 'destroy'])->middleware(['role:pemilik,admin', 'verified_owner'])->name('rooms.destroy');
        // Legacy aliases
        Route::post('/kos/{id}/kamar', [RoomController::class, 'store'])->middleware(['role:pemilik', 'verified_owner']);
        Route::put('/kamar/{id}', [RoomController::class, 'update'])->middleware(['role:pemilik,admin', 'verified_owner']);
        Route::delete('/kamar/{id}', [RoomController::class, 'destroy'])->middleware(['role:pemilik,admin', 'verified_owner']);

        // Bookings Flow
        Route::get('/bookings/export', [BookingController::class, 'exportCsv'])->name('bookings.export');
        Route::post('/bookings', [BookingController::class, 'store'])->middleware('role:pencari')->name('bookings.store');
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');
        Route::patch('/bookings/{id}/status', [BookingController::class, 'updateStatus'])->middleware('verified_owner')->name('bookings.update-status');
        // Legacy aliases
        Route::get('/pemesanan/export', [BookingController::class, 'exportCsv']);
        Route::post('/pemesanan', [BookingController::class, 'store'])->middleware('role:pencari');
        Route::get('/pemesanan', [BookingController::class, 'index']);
        Route::get('/pemesanan/{id}', [BookingController::class, 'show']);
        Route::patch('/pemesanan/{id}/status', [BookingController::class, 'updateStatus']);

        // Payment & Key Handover Flow
        Route::post('/bookings/{id}/checkout-dp', [PaymentController::class, 'checkoutDp'])->name('bookings.checkout-dp');
        Route::post('/bookings/{id}/checkout-pelunasan', [PaymentController::class, 'checkoutPelunasan'])->name('bookings.checkout-pelunasan');
        Route::post('/bookings/{id}/handover-key', [PaymentController::class, 'handoverKey'])->name('bookings.handover-key');
        Route::get('/bookings/{id}/payment-status', [PaymentController::class, 'checkStatus'])->name('bookings.payment-status');
        Route::post('/bookings/{id}/sync-status', [PaymentController::class, 'syncStatus'])->name('bookings.sync-status');
        // Legacy payment aliases
        Route::post('/pemesanan/{id}/checkout-dp', [PaymentController::class, 'checkoutDp']);
        Route::post('/pemesanan/{id}/checkout-pelunasan', [PaymentController::class, 'checkoutPelunasan']);
        Route::post('/pemesanan/{id}/handover-key', [PaymentController::class, 'handoverKey']);
        Route::get('/pemesanan/{id}/payment-status', [PaymentController::class, 'checkStatus']);
        Route::post('/pemesanan/{id}/sync-status', [PaymentController::class, 'syncStatus']);

        // Mitra Wallet & Balance (Pemilik)
        Route::middleware('role:pemilik')->group(function (): void {
            Route::get('/wallet/summary', [WalletController::class, 'summary'])->name('wallet.summary');
            Route::get('/wallet/mutations', [WalletController::class, 'mutations'])->name('wallet.mutations');
            Route::get('/wallet/bank-accounts', [WalletController::class, 'bankAccounts'])->name('wallet.bank-accounts');
            Route::post('/wallet/bank-accounts', [WalletController::class, 'addBankAccount'])->name('wallet.bank-accounts.store');
            Route::delete('/wallet/bank-accounts/{id}', [WalletController::class, 'deleteBankAccount'])->name('wallet.bank-accounts.destroy');
            Route::get('/wallet/withdrawals', [WalletController::class, 'withdrawals'])->name('wallet.withdrawals');
            Route::post('/wallet/withdrawals', [WalletController::class, 'requestWithdrawal'])->name('wallet.withdrawals.store');
        });

        // Manajemen Penghuni Aktif (Mitra Pemilik & Admin)
        Route::middleware(['role:pemilik,admin'])->group(function (): void {
            Route::get('/pemilik/penghuni', [BookingController::class, 'activeTenants'])->name('pemilik.penghuni');
            Route::post('/pemilik/penghuni/{id}/checkout', [BookingController::class, 'checkoutTenant'])->name('pemilik.penghuni.checkout');
        });

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

            // Property Ownership Verifications Moderation
            Route::get('/admin/verifications', [PropertyVerificationController::class, 'adminIndex'])->name('admin.verifications.index');
            Route::patch('/admin/verifications/{id}/status', [PropertyVerificationController::class, 'adminReview'])->name('admin.verifications.review');

            Route::get('/admin/stats', [AdminUserController::class, 'stats'])->name('admin.stats');

            // Kos Moderation
            Route::get('/admin/kos', [AdminKosController::class, 'index'])->name('admin.kos.index');
            Route::delete('/admin/kos/{id}', [AdminKosController::class, 'destroy'])->name('admin.kos.destroy');

            // Withdrawals Moderation
            Route::get('/admin/withdrawals', [AdminWithdrawalController::class, 'index'])->name('admin.withdrawals.index');
            Route::post('/admin/withdrawals/{id}/approve', [AdminWithdrawalController::class, 'approve'])->name('admin.withdrawals.approve');
            Route::post('/admin/withdrawals/{id}/reject', [AdminWithdrawalController::class, 'reject'])->name('admin.withdrawals.reject');

            // Hero Banner Slider Management
            Route::get('/admin/hero-banners', [HeroBannerController::class, 'index'])->name('admin.hero-banners.index');
            Route::post('/admin/hero-banners', [HeroBannerController::class, 'store'])->name('admin.hero-banners.store');
            Route::get('/admin/hero-banners/{id}', [HeroBannerController::class, 'show'])->name('admin.hero-banners.show');
            Route::put('/admin/hero-banners/{id}', [HeroBannerController::class, 'update'])->name('admin.hero-banners.update');
            Route::patch('/admin/hero-banners/{id}/status', [HeroBannerController::class, 'updateStatus'])->name('admin.hero-banners.update-status');
            Route::delete('/admin/hero-banners/{id}', [HeroBannerController::class, 'destroy'])->name('admin.hero-banners.destroy');
        });
    });
});
