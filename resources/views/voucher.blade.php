<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Voucher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Buat Voucher Hotspot</h1>
        <form id="voucherForm" method="POST" action="/voucher">
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
            <button type="submit" class="btn btn-primary">Buat Voucher</button>
        </form>
    
        <div id="responseMessage" class="mt-3"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
       $(document).ready(function() {
    $('#voucherForm').on('submit', function(event) {
        event.preventDefault();
        
        var formData = {
            username: $('#username').val(),
            password: $('#password').val(),
            profile: $('#profile').val(),
            duration: $('#duration').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

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
                    $('#responseMessage').html('<div class="alert alert-danger">Gagal: ' + 
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
    });
});

    </script>
</body>
</html>
