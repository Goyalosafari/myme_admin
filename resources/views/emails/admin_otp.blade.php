<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 30px; }
        .card { background: #fff; max-width: 480px; margin: auto; border-radius: 8px; padding: 40px; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .otp  { font-size: 36px; font-weight: bold; letter-spacing: 8px; color: #435ebe; text-align: center; margin: 24px 0; }
        p     { color: #555; line-height: 1.6; }
        small { color: #999; }
    </style>
</head>
<body>
    <div class="card">
        <p>Hello Admin,</p>
        <p>Use the OTP below to complete your login. It expires in <strong>5 minutes</strong>.</p>
        <div class="otp">{{ $otp }}</div>
        <p>If you did not request this, please ignore this email.</p>
        <small>{{ config('app.name') }} — Admin Panel</small>
    </div>
</body>
</html>
