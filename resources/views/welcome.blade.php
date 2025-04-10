<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="logo.png" type="image/x-icon">
    <title>Voucher</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <style>
        /* Menggunakan font Poppins untuk seluruh halaman */
        body {
            font-family: 'Poppins', sans-serif;
        }

        h1 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: #333;
        }

        .btn {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body>

<div class="container mx-auto px-6 py-12">
    @csrf
    <div id="voucherResult" class="max-w-xl mx-auto my-4"></div>
    <!-- Judul -->
    <h1 class="text-4xl font-bold text-center text-gray-900 mb-10">Pilihan Voucher Internet</h1>

    <!-- Gambar Banner -->
    <div class="bg-gradient-to-r from-purple-400 via-pink-500 to-red-500 rounded-lg overflow-hidden shadow-lg">
        <img src="banner.jpg" class="w-full h-64 sm:h-80 md:h-96 transform transition-all duration-500 hover:scale-105" alt="Gambar Banner">
    </div>

    <!-- Pilihan Voucher -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 mt-8 px-4">
        <!-- Card Voucher Guru -->
        <div class="bg-white rounded-lg shadow-xl transform transition-all duration-300 hover:scale-105 hover:shadow-2xl">
            <img src="logo.png" class="w-full h-48 object-cover rounded-t-lg transition-all duration-300 hover:opacity-80" alt="Voucher 1 Jam">
            <div class="p-6">
                <h5 class="text-2xl font-semibold text-gray-800">Voucher 1 Jam</h5>
                <p class="text-gray-600 mt-3">Voucher akses hotspot selama 1 jam dengan kecepatan 2Mb.</p>
                <button type="button" onclick="generateVoucher('1jam', 10)" class="mt-6 btn w-full bg-gradient-to-r from-indigo-500 to-purple-500 text-white px-6 py-3 rounded-lg transform transition-all duration-300 hover:scale-105 hover:from-indigo-600 hover:to-purple-600">Beli Voucher Guru</button>
            </div>
        </div>

        <!-- Card Voucher 2 Jam -->
        <div class="bg-white rounded-lg shadow-xl transform transition-all duration-300 hover:scale-105 hover:shadow-2xl">
            <img src="logo.png" class="w-full h-48 object-cover rounded-t-lg transition-all duration-300 hover:opacity-80" alt="Voucher 2 Jam">
            <div class="p-6">
                <h5 class="text-2xl font-semibold text-gray-800">Voucher 2 Jam</h5>
                <p class="text-gray-600 mt-3">Voucher akses hotspot selama 2 jam dengan kecepatan 2Mb.</p>
                <button type="button" onclick="generateVoucher('2jam', 20)" class="mt-6 btn w-full bg-gradient-to-r from-indigo-500 to-purple-500 text-white px-6 py-3 rounded-lg transform transition-all duration-300 hover:scale-105 hover:from-indigo-600 hover:to-purple-600">Beli Voucher 2 Jam</button>
            </div>
        </div>

        <!-- Card Voucher 1 Hari -->
        <div class="bg-white rounded-lg shadow-xl transform transition-all duration-300 hover:scale-105 hover:shadow-2xl">
            <img src="logo.png" class="w-full h-48 object-cover rounded-t-lg transition-all duration-300 hover:opacity-80" alt="Voucher 1 Hari">
            <div class="p-6">
                <h5 class="text-2xl font-semibold text-gray-800">Voucher 1 Hari</h5>
                <p class="text-gray-600 mt-3">Voucher akses hotspot selama 1 hari penuh.</p>
                <button type="button" onclick="generateVoucher('1hari', 240)" class="mt-6 btn w-full bg-gradient-to-r from-indigo-500 to-purple-500 text-white px-6 py-3 rounded-lg transform transition-all duration-300 hover:scale-105 hover:from-indigo-600 hover:to-purple-600">Beli Voucher 1 Hari</button>
            </div>
        </div>
    </div>

</div>
<script>
    function generateRandomString(length) {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let result = '';
        for (let i = 0; i < length; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return result;
    }

    function generateVoucher(profile, price) {
        // Step 1: Generate credential (username & password)
        const credential = 'user_' + generateRandomString(6);

        // Step 2: Minta snap token dari backend
        fetch('/generate-midtrans-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content 
            },
            body: JSON.stringify({
                username: credential,
                password: credential,
                profile: profile,
                price: price
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.snap_token) {
                // Step 3: Buka Snap popup
                snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        // Step 4: Tampilkan voucher div
                        showVoucherDiv(credential, profile);
                    },
                    onPending: function(result) {
                        alert("Pembayaran sedang diproses...");
                    },
                    onError: function(result) {
                        alert("Pembayaran gagal. Silakan coba lagi.");
                    }
                });
            } else {
                alert("Gagal mendapatkan snap token.");
            }
        })
        .catch(err => {
            console.error(err);
            alert("Terjadi kesalahan saat memulai pembayaran.");
        });
    }

    function showVoucherDiv(credential, profile) {
        document.getElementById('voucherResult').innerHTML = `
            <div class="bg-green-100 text-green-800 p-4 rounded-lg mt-6 shadow-md">
                <h2 class="text-xl font-bold mb-2">Voucher Berhasil Dibuat!</h2>
                <p>Username & Password: <strong>${credential}</strong></p>
                <p>Profile: <strong>${profile}</strong></p>
            </div>
        `;
    }
</script>

</body>
</html>