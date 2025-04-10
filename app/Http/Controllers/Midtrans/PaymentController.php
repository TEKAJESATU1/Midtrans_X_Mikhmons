<?php

namespace App\Http\Controllers\Midtrans;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function createTransaction()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        $params = [
            'transaction_details' => [
                'order_id' => 'order-' . rand(),
                'gross_amount' => 1000,
            ],
            'customer_details' => [
                'first_name' => 'Wann',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'phone' => '08123456789',
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return view('payment', ['snapToken' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function handleCallback(Request $request)
    {
        $notification = json_decode($request->getContent());

        $orderId = $notification->order_id;
        $transactionStatus = $notification->transaction_status;

        if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
            // Update status pembayaran di database atau Mikhmon
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error'], 400);
    }

    public function generateMidtransToken(Request $request) {
        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = true;
        Config::$isSanitized = true;
        Config::$is3ds = true;
        
        // Data transaksi
        $transaction = [
            'transaction_details' => [
                'order_id' => uniqid(), // ID unik untuk transaksi
                'gross_amount' => $request->price, // Harga dari frontend
            ],
            'customer_details' => [
                'first_name' => 'Pelanggan',
                'email' => 'pelanggan@example.com',
                'phone' => '08123456789',
            ],
            'item_details' => [
                [
                    'id' => 'voucher-' . $request->profile,
                    'price' => $request->price,
                    'quantity' => 1,
                    'name' => 'Voucher Hotspot ' . $request->profile,
                ]
            ]
        ];
        
        try {
            $snapToken = Snap::getSnapToken($transaction);
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
