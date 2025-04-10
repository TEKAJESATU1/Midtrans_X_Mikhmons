<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <!-- Menyertakan Snap.js dari Midtrans -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
</head>
<body>
    <h1>Payment Page</h1>

    <div>
        <p>Total Amount: Rp 100,000</p>
        <!-- Tombol Pembayaran -->
        <button id="pay-button">Pay Now</button>
    </div>

    <script>
        // Mendapatkan token Snap yang diberikan oleh backend
        var snapToken = '{{ $snapToken }}'; // Token yang dikirim dari backend

        // Event listener untuk tombol pembayaran
        document.getElementById('pay-button').onclick = function () {
            snap.pay(snapToken, {
                onSuccess: function(result) {
                    console.log('Payment success:', result);
                    // Kamu bisa mengirim hasil pembayaran ke backend untuk verifikasi lebih lanjut
                },
                onPending: function(result) {
                    console.log('Payment pending:', result);
                    // Beritahu pengguna jika pembayaran dalam status pending
                },
                onError: function(result) {
                    console.log('Payment error:', result);
                    // Tangani error pembayaran
                }
            });
        };
    </script>
</body>
</html>
