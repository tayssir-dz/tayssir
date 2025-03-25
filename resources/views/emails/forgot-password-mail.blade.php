<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <title>{{ config('app.name') }} - نسيت كلمة المرور</title>
    <meta charset="UTF-8">
</head>

<body style="text-align: right; font-family: Arial, sans-serif;">
    <h1>نسيت كلمة المرور</h1>
    <p>عزيزي {{ $mailData['name'] }}،</p>
    <p>لقد تلقينا طلبًا لإعادة تعيين كلمة المرور الخاصة بك في {{ config('app.name') }}. للمتابعة في إعادة تعيين كلمة
        المرور، يرجى إدخال كلمة المرور لمرة واحدة (OTP) المقدمة أدناه:</p>
    <h2>{{ $mailData['otp'] }}</h2>
    <p>إذا لم تطلب إعادة تعيين كلمة المرور، يرجى تجاهل هذا البريد الإلكتروني.</p>
    <br />
    <p>مع أطيب التحيات،</p>
    <p>فريق {{ config('app.name') }}</p>
</body>

</html>
