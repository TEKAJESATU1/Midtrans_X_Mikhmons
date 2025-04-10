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
    <div id="voucherResult" class="max-w-xl mx-auto mt-4"></div>
    <!-- Judul -->
    <!-- <h1 class="text-5xl font-extrabold text-center text-gray-900 mb-10">Pilihan Voucher Internet</h1> -->

    <!-- Gambar Banner -->
    <div class="mt-4 bg-gradient-to-r from-purple-400 via-pink-500 to-red-500 rounded-lg overflow-hidden shadow-lg h-auto sm:h-auto xs:h-[12px]">
        <img src="Banner_Voucher.png" class="w-full transform transition-all duration-500 hover:scale-105" alt="Gambar Banner">
    </div>


    <!-- Pilihan Voucher -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 mt-8 px-4">
        <!-- Card Voucher Guru -->
        <div class="bg-white rounded-lg shadow-xl transform transition-all duration-300 hover:scale-105 hover:shadow-2xl">
            <img src="voucher1.png" class="w-full h-48 object-cover rounded-t-lg transition-all duration-300 hover:opacity-80" alt="Voucher 1 Jam">
            <div class="p-6">
                <h5 class="text-2xl font-semibold text-gray-800">Voucher 1 Jam</h5>
                <p class="text-gray-600 mt-3">Voucher akses hotspot selama 1 jam dengan kecepatan 10Mb.</p>
                <button type="button" onclick="generateVoucher('1jam', 10)" class="mt-6 btn w-full bg-gradient-to-r from-[#43b0f1] to-[#057DCD] text-white px-6 py-3 rounded-lg transform transition-all duration-300 hover:scale-105 hover:from-[#1e3d58] hover:to-[#057DCD]">Beli Voucher Guru</button>
            </div>
        </div>

        <!-- Card Voucher 2 Jam -->
        <div class="bg-white rounded-lg shadow-xl transform transition-all duration-300 hover:scale-105 hover:shadow-2xl">
            <img src="voucher2.png" class="w-full h-48 object-cover rounded-t-lg transition-all duration-300 hover:opacity-80" alt="Voucher 2 Jam">
            <div class="p-6">
                <h5 class="text-2xl font-semibold text-gray-800">Voucher 2 Jam</h5>
                <p class="text-gray-600 mt-3">Voucher akses hotspot selama 2 jam dengan kecepatan 10Mb.</p>
                <button type="button" onclick="generateVoucher('2jam', 20)" class="mt-6 btn w-full bg-gradient-to-r from-[#43b0f1] to-[#057DCD] text-white px-6 py-3 rounded-lg transform transition-all duration-300 hover:scale-105 hover:from-[#1e3d58] hover:to-[#057DCD]">Beli Voucher 2 Jam</button>
            </div>
        </div>

        <!-- Card Voucher 1 Hari -->
        <div class="bg-white rounded-lg shadow-xl transform transition-all duration-300 hover:scale-105 hover:shadow-2xl">
            <img src="voucher3.png" class="w-full h-48 object-cover rounded-t-lg transition-all duration-300 hover:opacity-80" alt="Voucher 1 Hari">
            <div class="p-6">
                <h5 class="text-2xl font-semibold text-gray-800">Voucher 1 Hari</h5>
                <p class="text-gray-600 mt-3">Voucher akses hotspot selama 1 hari penuh dengan kecepatan 10Mb.</p>
                <button type="button" onclick="generateVoucher('1hari', 240)" class="mt-6 btn w-full bg-gradient-to-r from-[#43b0f1] to-[#057DCD] text-white px-6 py-3 rounded-lg transform transition-all duration-300 hover:scale-105 hover:from-[#1e3d58] hover:to-[#057DCD]">Beli Voucher 1 Hari</button>
            </div>
        </div>
    </div>

</div>
<script>
    function generateRandomString(length) {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let result = '';
        for (let i = 0; i < length; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return result;
    }

    function generateVoucher(profile, price) {
        // Step 1: Generate credential (username & password)
        const credential = generateRandomString(5);

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
                <h2 class="text-xl font-semibold mb-2 text-center">Voucher Berhasil Dibuat!</h2>
                <div class="flex mx-5">
                    <ul class="">
                        <li class="list-disc">Pilih Jaringan Wifi dengan SSID XII TJKT 1</li>
                        <li class="list-disc">Jika terdapat notif masuk ke jaringan klik dan masukan username dan password dibawah</li>
                        <li class="list-disc">Jika tidak ada notif, buka browser lalu ketika domain http://tjktsatu.com</li>
                    </ul>
                </div>
                <div class="w-full h-1 bg-green-800 rounded-full my-2"></div>
                <p class="text-lg font-semibold">Username & Password: <strong>${credential}</strong></p>
                <p class="text-lg font-semibold">Profile: <strong>${profile}</strong></p>
            </div>
        `;
    }
</script>

</body>
</html>