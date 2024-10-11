<!DOCTYPE html>
<html>

<head>
    <title>{{ env('APP_NAME') }} - Email Verification</title>
</head>

<body>
    <h1>Email Verification</h1>
    <p>Dear {{ $mailData['name'] }},</p>
    <p>Thank you for signing up with {{ env('APP_NAME') }}. To complete your registration, please enter the One-Time
        Password (OTP)
        provided below:</p>
    <h2>{{ $mailData['otp'] }}</h2>
    <p>If you did not request this verification, please ignore this email.</p>
    <br />
    <p>Best regards,</p>
    <p>The {{ env('APP_NAME') }} Team</p>
</body>

</html>
