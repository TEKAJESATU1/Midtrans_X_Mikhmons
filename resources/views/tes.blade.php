<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="logo.png" type="image/x-icon">
    <title>Voucher</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <!-- Pilihan 1 -->
    <div class="container">
        <h1 class="my-4">Pilihan 1</h1>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="text" class="form-control" id="password" name="password" required>
        </div>

        <!-- Pilihan Voucher dalam Bentuk Card -->
        <div class="row my-4">
            <div class="col-md-4">
                <div class="card">
                    <img src="logo.png" class="card-img-top" alt="Guru Voucher">
                    <div class="card-body">
                        <h5 class="card-title">Voucher Guru</h5>
                        <p class="card-text">Voucher untuk Guru, akses hotspot tanpa batasan waktu.</p>
                        <input type="radio" id="profileGuru" name="profile" value="Guru" required>
                        <label for="profileGuru">Pilih Voucher Guru</label>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <img src="logo.png" class="card-img-top" alt="2 Jam Voucher">
                    <div class="card-body">
                        <h5 class="card-title">Voucher 2 Jam</h5>
                        <p class="card-text">Voucher untuk akses hotspot selama 2 jam. Dengan kecepatan 2Mb</p>
                        <input type="radio" id="profile2jam" name="profile" value="2jam" required>
                        <label for="profile2jam">Pilih Paket 2 Jam</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>
    <hr>
    
    <!-- Pilihan 2 -->
    <div class="container">
    <h1 class="my-4">Pilihan 2</h1>

    <!-- Pilihan Voucher dalam Bentuk Card -->
    <div class="row my-4">
        <div class="col-md-4">
            <div class="card">
                <img src="logo.png" class="card-img-top" alt="Guru Voucher">
                <div class="card-body">
                    <h5 class="card-title">Voucher Guru</h5>
                    <p class="card-text">Voucher untuk Guru, akses hotspot tanpa batasan waktu.</p>
                    <button type="button" class="btn btn-primary" onclick="selectVoucher('Guru')">Beli Voucher Guru</button>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <img src="logo.png" class="card-img-top" alt="2 Jam Voucher">
                <div class="card-body">
                    <h5 class="card-title">Voucher 2 Jam</h5>
                    <p class="card-text">Voucher untuk akses hotspot selama 2 jam. Dengan kecepatan 2Mb</p>
                    <button type="button" class="btn btn-primary" onclick="selectVoucher('2jam')">Beli Voucher 2 Jam</button>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <img src="logo.png" class="card-img-top" alt="1 Hari Voucher">
                <div class="card-body">
                    <h5 class="card-title">Voucher 1 Hari</h5>
                    <p class="card-text">Voucher untuk akses hotspot selama 1 hari penuh.</p>
                    <button type="button" class="btn btn-primary" onclick="selectVoucher('1hari')">Beli Voucher 1 Hari</button>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>