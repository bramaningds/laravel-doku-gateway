<?php

use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\PaymentController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/payment/{invoice}', PaymentController::class);
Route::any('/callback', PaymentCallbackController::class)->withoutMiddleware(VerifyCsrfToken::class);

Route::get('test-payment', function () {
    return view('test-payment');
});
