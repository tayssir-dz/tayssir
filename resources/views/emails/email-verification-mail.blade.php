<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <title>{{ env('APP_NAME') }} - تحقق من البريد الإلكتروني</title>
    <meta charset="UTF-8">
</head>

<body style="text-align: right; font-family: Arial, sans-serif;">
    <h1>تحقق من البريد الإلكتروني</h1>
    <p>عزيزي {{ $mailData['name'] }}،</p>
    <p>شكرًا لتسجيلك في {{ env('APP_NAME') }}. لإكمال تسجيلك، يرجى إدخال كلمة المرور لمرة واحدة (OTP) المقدمة أدناه:</p>
    <h2>{{ $mailData['otp'] }}</h2>
    <p>إذا لم تطلب هذا التحقق، يرجى تجاهل هذا البريد الإلكتروني.</p>
    <br />
    <p>مع أطيب التحيات،</p>
    <p>فريق {{ env('APP_NAME') }}</p>
</body>

</html>
