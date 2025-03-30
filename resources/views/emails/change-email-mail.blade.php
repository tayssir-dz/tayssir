<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <title>{{ config('app.name') }} - تغيير البريد الإلكتروني</title>
    <meta charset="UTF-8">
</head>

<body style="text-align: right; font-family: Arial, sans-serif;">
    <h1>تغيير البريد الإلكتروني</h1>
    <p>عزيزي {{ $mailData['name'] }}،</p>
    <p>لقد تلقينا طلبًا لتغيير بريدك الإلكتروني في {{ config('app.name') }}. لإكمال العملية، يرجى إدخال كلمة المرور لمرة
        واحدة (OTP) المقدمة أدناه:
    </p>
    <h2>{{ $mailData['otp'] }}</h2>
    <p>إذا لم تطلب هذا التغيير، يرجى تجاهل هذا البريد الإلكتروني وتأمين حسابك.</p>
    <br />
    <p>مع أطيب التحيات،</p>
    <p>فريق {{ config('app.name') }}</p>
</body>

</html>
