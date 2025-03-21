<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Midtrans\PaymentController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/midtrans/payment', [PaymentController::class, 'createTransaction']);
Route::post('/midtrans/callback', [PaymentController::class, 'handleCallback']);
