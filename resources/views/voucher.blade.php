<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Voucher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Midtrans Snap JS -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
</head>
<body>
    <div class="container">
        <h1 class="my-4">Buat Voucher Hotspot</h1>
        <form id="voucherForm" method="POST">
            @csrf
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="text" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="profile" class="form-label">Profile</label>
                <select class="form-select" id="profile" name="profile" required>
                    <option value="Guru">Guru</option>
                    <option value="2jam">2 Jam</option>
                    <option value="1hari">1 Hari</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="duration" class="form-label">Durasi (menit)</label>
                <input type="number" class="form-control" id="duration" name="duration" value="60" required>
            </div>
            <button type="button" id="pay-button" class="btn btn-primary">Bayar dan Buat Voucher</button>
        </form>
    
        <div id="responseMessage" class="mt-3"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Ganti tombol submit dengan tombol pembayaran Midtrans
            $('#pay-button').on('click', function() {
                // Kumpulkan data form
                var formData = {
                    username: $('#username').val(),
                    password: $('#password').val(),
                    profile: $('#profile').val(),
                    duration: $('#duration').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                };
                
                // Hitung harga berdasarkan profile (contoh sederhana)
                var price = 10000; // Harga default
                if(formData.profile === 'Guru') {
                    price = 5000;
                } else if(formData.profile === '2jam') {
                    price = 10000;
                } else if(formData.profile === '1hari') {
                    price = 20000;
                }
                
                // Kirim data ke backend untuk mendapatkan snapToken
                $.ajax({
                    url: '/generate-midtrans-token', // Endpoint baru di backend
                    type: 'POST',
                    data: {
                        ...formData,
                        price: price
                    },
                    success: function(response) {
                        if(response.snap_token) {
                            // Buka popup pembayaran Midtrans
                            snap.pay(response.snap_token, {
                                onSuccess: function(result) {
                                    console.log('Payment success:', result);
                                    // Jika pembayaran berhasil, buat voucher
                                    createVoucher(formData);
                                },
                                onPending: function(result) {
                                    console.log('Payment pending:', result);
                                    $('#responseMessage').html('<div class="alert alert-warning">Pembayaran sedang diproses. Voucher akan dibuat setelah pembayaran selesai.</div>');
                                },
                                onError: function(result) {
                                    console.log('Payment error:', result);
                                    $('#responseMessage').html('<div class="alert alert-danger">Pembayaran gagal: ' + result.status_message + '</div>');
                                },
                                onClose: function() {
                                    console.log('Payment popup closed');
                                }
                            });
                        } else {
                            $('#responseMessage').html('<div class="alert alert-danger">Gagal memproses pembayaran: ' + (response.error || 'Unknown error') + '</div>');
                        }
                    },
                    error: function(xhr) {
                        var errorMsg = xhr.responseJSON && xhr.responseJSON.error 
                            ? xhr.responseJSON.error 
                            : 'Terjadi kesalahan pada server';
                        console.error('Error:', xhr.responseText);
                        $('#responseMessage').html('<div class="alert alert-danger">' + errorMsg + '</div>');
                    }
                });
            });
            
            // Fungsi untuk membuat voucher setelah pembayaran berhasil
            function createVoucher(formData) {
                $.ajax({
                    url: '/voucher',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if(response.success) {
                            $('#responseMessage').html('<div class="alert alert-success">' + 
                                response.message + '<br>Username: ' + formData.username + 
                                '<br>Password: ' + formData.password + '</div>');
                            $('#voucherForm')[0].reset();
                        } else {
                            $('#responseMessage').html('<div class="alert alert-danger">Gagal membuat voucher: ' + 
                                (response.error || 'Unknown error') + '</div>');
                        }
                    },
                    error: function(xhr) {
                        var errorMsg = xhr.responseJSON && xhr.responseJSON.error 
                            ? xhr.responseJSON.error 
                            : 'Terjadi kesalahan pada server';
                        console.error('Error:', xhr.responseText);
                        $('#responseMessage').html('<div class="alert alert-danger">' + errorMsg + '</div>');
                    }
                });
            }
        });
    </script>
</body>
</html>