<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Midtrans\PaymentController;
use App\Http\Controllers\VoucherController;
use RouterOS\Client;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/beli', function () {
    return view('voucher');
});
Route::get('/midtrans/payment', [PaymentController::class, 'createTransaction']);
Route::get('/payment', [PaymentController::class, 'createTransaction']);
Route::post('/generate-midtrans-token', [PaymentController::class, 'generateMidtransToken']);
Route::post('/midtrans/callback', [PaymentController::class, 'handleCallback']);
Route::post('/voucher', [VoucherController::class, 'createVoucher']);
// routes/web.php
Route::get('/test-mikrotik', function() {
    try {
        $client = new Client([
            'host' => env('MIKROTIK_HOST'),
            'user' => env('MIKROTIK_USERNAME'),
            'pass' => env('MIKROTIK_PASSWORD'),
            'port' => (int)env('MIKROTIK_PORT', 8728),
        ]);
        
        $client->connect();
        return response()->json(['status' => 'Connected successfully']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});