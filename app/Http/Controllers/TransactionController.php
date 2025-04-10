<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Transaction;
use App\Models\Voucher;

class TransactionController extends Controller
{
    public function create(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'profile' => 'required',
            'duration' => 'required|numeric',
            'price' => 'required|numeric'
        ]);

        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Buat order ID unik
        $orderId = uniqid('VOUCHER-');

        // Simpan transaksi ke database
        $transaction = Transaction::create([
            'order_id' => $orderId,
            'status' => 'pending',
            'price' => $request->price,
            'username' => $request->username,
            'password' => $request->password,
            'profile' => $request->profile,
            'duration' => $request->duration
        ]);

        // Data untuk Midtrans
        $transactionDetails = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $request->price,
            ],
            'customer_details' => [
                'first_name' => 'Customer',
                'email' => 'customer@example.com',
                'phone' => '08123456789',
            ],
            'item_details' => [
                [
                    'id' => 'voucher-' . $request->profile,
                    'price' => $request->price,
                    'quantity' => 1,
                    'name' => 'Voucher ' . $request->profile,
                ]
            ]
        ];

        try {
            $snapToken = Snap::getSnapToken($transactionDetails);
            $transaction->update(['snap_token' => $snapToken]);
            
            return response()->json([
                'snap_token' => $snapToken,
                'order_id' => $orderId
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function handleSuccess($orderId, Request $request)
{
    try {
        $transaction = Transaction::where('order_id', $orderId)->firstOrFail();

        if ($transaction->isSuccess()) {
            return response()->json([
                'message' => 'Voucher sudah dibuat sebelumnya',
                'voucher' => [
                    'username' => $transaction->username,
                    'password' => $transaction->password,
                    'profile' => $transaction->profile,
                    'duration' => $transaction->duration
                ]
            ]);
        }

        // Buat voucher di MikroTik
        $mikrotikResponse = $transaction->createVoucher();
        
        // Simpan ke database
        $voucher = Voucher::create([
            'transaction_id' => $transaction->id,
            'username' => $transaction->username,
            'password' => $transaction->password,
            'profile' => $transaction->profile,
            'duration' => $transaction->duration,
            'expires_at' => now()->addMinutes($transaction->duration)
        ]);

        // Update status transaksi
        $transaction->update([
            'status' => 'success',
            'payload' => json_encode($mikrotikResponse)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil dibuat',
            'voucher' => $voucher,
            'mikrotik_response' => $mikrotikResponse
        ]);

    } catch (\Exception $e) {
        \Log::error('Voucher Error: ' . $e->getMessage(), [
            'order_id' => $orderId,
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error membuat voucher: ' . $e->getMessage(),
            'error_details' => $e->getTrace()
        ], 500);
    }
}

    private function createVoucher($transaction)
    {
        // 1. Simpan ke database lokal
        $voucher = Voucher::create([
            'transaction_id' => $transaction->id,
            'username' => $transaction->username,
            'password' => $transaction->password,
            'profile' => $transaction->profile,
            'duration' => $transaction->duration,
            'expires_at' => now()->addMinutes($transaction->duration)
        ]);

        // 2. Sync ke MikroTik
        $this->createMikrotikHotspotUser(
            $transaction->username,
            $transaction->password,
            $transaction->profile,
            $transaction->duration
        );

        return $voucher;
    }

    public function checkStatus($orderId)
    {
        $transaction = Transaction::where('order_id', $orderId)->firstOrFail();
        $status = $this->checkMidtransStatus($orderId);

        return response()->json([
            'status' => $status,
            'transaction' => $transaction
        ]);
    }

    private function checkMidtransStatus($orderId)
    {
        // Implementasi pengecekan status ke Midtrans API
        // Ini adalah contoh sederhana, implementasi sebenarnya perlu menggunakan Midtrans API
        return 'settlement'; // Contoh saja
    }

    private function createMikrotikHotspotUser($username, $password, $profile, $duration)
    {
        try {
            $client = new \RouterOS\Client([
                'host' => env('MIKROTIK_HOST'),
                'user' => env('MIKROTIK_USERNAME'),
                'pass' => env('MIKROTIK_PASSWORD'),
                'port' => (int) env('MIKROTIK_PORT', 8728),
            ]);

            $response = $client->query('/ip/hotspot/user/add', [
                'name' => $username,
                'password' => $password,
                'profile' => $profile,
                'limit-uptime' => $duration * 60,
            ])->read();

            \Log::info('MikroTik response:', $response);
            
        } catch (\Exception $e) {
            \Log::error('Failed to create MikroTik user: '.$e->getMessage());
            throw $e;
        }
    }
}