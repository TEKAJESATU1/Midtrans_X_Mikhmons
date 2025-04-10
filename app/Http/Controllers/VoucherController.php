<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RouterOS\Client;
use RouterOS\Query;

class VoucherController extends Controller
{
    protected $client;

    public function __construct()
    {
        try {
            $this->client = new Client([
                'host' => env('MIKROTIK_HOST', '192.168.88.1'),
                'user' => env('MIKROTIK_USERNAME', 'admin'),
                'pass' => env('MIKROTIK_PASSWORD', ''),
                'port' => (int)env('MIKROTIK_PORT', 8728),
            ]);
        } catch (\Exception $e) {
            abort(500, 'Gagal terhubung ke MikroTik: ' . $e->getMessage());
        }
    }

    public function createVoucher(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'profile' => 'required|string|max:255',
        ]);
    
        try {
            // Untuk Hotspot (bukan PPPoE)
            $query = (new Query('/ip/hotspot/user/add'))
                ->equal('name', $validated['username'])
                ->equal('password', $validated['password'])
                ->equal('profile', $validated['profile'])
                ->equal('limit-uptime', '1h') // Durasi voucher (contoh: 1 jam)
                ->equal('server', 'hotspot1'); // atau nama server hotspot Anda
    
            $response = $this->client->query($query)->read();
    
            // Simpan perubahan
            $this->client->query(new Query('/system/config/save'))->read();
    
            return response()->json([
                'success' => true,
                'message' => 'Voucher berhasil dibuat',
                'data' => $response
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal membuat voucher: ' . $e->getMessage()
            ], 500);
        }
    }
}