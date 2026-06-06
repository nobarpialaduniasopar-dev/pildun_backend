<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Api\MatchController as PublicMatchController;

use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\MatchController as AdminMatchController;
use App\Http\Controllers\Api\Admin\TransactionController;
use App\Http\Controllers\Api\Admin\ScannerController;
use App\Http\Controllers\Api\Admin\StandingController;

Route::prefix('v1')->group(function () {
    // Flow Publik
    Route::get('/matches', [PublicMatchController::class, 'index']);
    Route::get('/standings', [StandingController::class, 'index']);
    Route::post('/otp/send', [OtpController::class, 'send']);
    Route::post('/otp/verify', [OtpController::class, 'verify']);
    Route::post('/checkout', [CheckoutController::class, 'process']);
    Route::get('/checkout/detail/{order_id}', [CheckoutController::class, 'show']);
    Route::post('/webhook/midtrans', [WebhookController::class, 'handle']);

    // Flow Admin
    Route::prefix('admin')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);

        // Harus Login Menggunakan Sanctum Token
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            
            // CRUD Match
            Route::apiResource('matches', AdminMatchController::class);
            
            // Transaksi & Export
            Route::get('/transactions', [TransactionController::class, 'index']);
            Route::get('/transactions/export', [TransactionController::class, 'exportCsv']);
            
            // Gatekeeper
            Route::post('/scanner/scan', [ScannerController::class, 'scan']);
            Route::post('/scanner/checkout', [ScannerController::class, 'checkout']);

            // Klasemen & Bagan
            Route::get('/standings', [StandingController::class, 'index']);
            Route::put('/standings/{standing}', [StandingController::class, 'updateManual']);
            Route::post('/standings/sync', [StandingController::class, 'syncExternal']);
        });
    });
});