@php($title = config('app.name') . ' - تحقق من البريد الإلكتروني')
@php($preheader = 'رمز التحقق من البريد الإلكتروني الخاص بك: ' . ($mailData['otp'] ?? ''))
@component('emails.layout', compact('title', 'preheader'))
    <h1>تحقق من البريد الإلكتروني</h1>
    <p>عزيزي {{ $mailData['name'] }}،</p>
    <p>شكرًا لتسجيلك في {{ config('app.name') }}. لإكمال تسجيلك، يرجى إدخال كلمة المرور لمرة واحدة (OTP) المقدمة أدناه:</p>
    @include('emails.components.code', ['code' => $mailData['otp']])
    <p>إذا لم تطلب هذا التحقق، يرجى تجاهل هذا البريد الإلكتروني.</p>
    @include('emails.components.spacer', ['size' => 20])
    <p style="margin:0;">مع أطيب التحيات،</p>
    <p style="margin:0;">فريق {{ config('app.name') }}</p>
@endcomponent
