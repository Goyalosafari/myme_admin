<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OTP</title>
</head>
<body style="background-color: white; font-family: system-ui;">
  <center>
    <div style="background-color: #09992b; padding: 1rem 0;">
      <img src="{{ asset('images/Group 180.png') }}" style="width: 3rem;">
    </div>
    <div><img src="https://firebasestorage.googleapis.com/v0/b/guzty-c2dc5.appspot.com/o/assets%2FGroup%20119.png?alt=media&token=783aeeab-d429-4bd7-9985-6703b84d0e7f" style="margin: 2rem 0 1rem 0;"></div>
    <div style="padding: 0 2rem;">
      <p>{{ isset($message) ? $message : 'Thank you for registering with Myme App! Please use the following OTP:' }}</p>
    </div>
    <h2>{{ $otp }}</h2>
    <div style="color: #9e9898; padding: 0 2rem; font-size: 0.7rem;">
      <p>This OTP is valid for 30 minutes. Do not share it with anyone.</p>
      <p>If you didn’t request it, ignore this message.</p>
    </div>
  </center>
</body>
</html>
