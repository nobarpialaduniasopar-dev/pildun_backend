<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\WebhookController;

Route::prefix('v1')->group(function () {
    // Flow Publik (Tanpa Login)
    Route::post('/otp/send', [OtpController::class, 'send']);
    Route::post('/otp/verify', [OtpController::class, 'verify']);
    Route::post('/checkout', [CheckoutController::class, 'process']);
    
    // Webhook Midtrans
    Route::post('/webhook/midtrans', [WebhookController::class, 'handle']);
});