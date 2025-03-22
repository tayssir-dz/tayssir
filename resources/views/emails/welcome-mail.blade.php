<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <title>{{ env('APP_NAME') }} - رسالة ترحيب</title>
    <meta charset="UTF-8">
</head>

<body style="text-align: right; font-family: Arial, sans-serif;">
    <h1>مرحبًا بك في {{ env('APP_NAME') }}</h1>
    <p>عزيزي {{ $mailData['name'] }}،</p>
    <p>نشكرك على انضمامك إلى {{ env('APP_NAME') }}. يسعدنا أن نرحب بك في مجتمعنا.</p>
    <p>يمكنك الآن الاستفادة من جميع الخدمات والميزات المتاحة في منصتنا.</p>
    <br />
    <p>مع أطيب التحيات،</p>
    <p>فريق {{ env('APP_NAME') }}</p>
</body>

</html>
