<!DOCTYPE html>
<html>

<head>
    <title>{{ env('APP_NAME') }} - Forgot Password</title>
</head>

<body>
    <h1>Forgot Password</h1>
    <p>Dear {{ $mailData['name'] }},</p>
    <p>We received a request to reset your password for {{ env('APP_NAME') }}. To proceed with the password reset,
        please enter the One-Time
        Password (OTP)
        provided below:</p>
    <h2>{{ $mailData['otp'] }}</h2>
    <p>If you did not request a password reset, please ignore this email.</p>
    <br />
    <p>Best regards,</p>
    <p>The {{ env('APP_NAME') }} Team</p>
</body>

</html>
