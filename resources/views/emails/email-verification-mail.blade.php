<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <title>{{ config('app.name') }} - تحقق من البريد الإلكتروني</title>
    <meta charset="UTF-8">
</head>

<body style="text-align: right; font-family: Arial, sans-serif;">
    <h1>تحقق من البريد الإلكتروني</h1>
    <p>عزيزي {{ $mailData['name'] }}،</p>
    <p>شكرًا لتسجيلك في {{ config('app.name') }}. لإكمال تسجيلك، يرجى إدخال كلمة المرور لمرة واحدة (OTP) المقدمة أدناه:
    </p>
    <h2>{{ $mailData['otp'] }}</h2>
    <p>إذا لم تطلب هذا التحقق، يرجى تجاهل هذا البريد الإلكتروني.</p>
    <br />
    <p>مع أطيب التحيات،</p>
    <p>فريق {{ config('app.name') }}</p>
</body>

</html>
