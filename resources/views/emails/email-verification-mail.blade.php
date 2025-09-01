@php($title = config('app.name') . ' - تحقق من البريد الإلكتروني')
@php($preheader = 'رمز التحقق من البريد الإلكتروني الخاص بك: ' . ($mailData['otp'] ?? ''))
@component('emails.layout', compact('title', 'preheader'))
    <h1 style="direction: rtl; unicode-bidi: embed; text-align: right;">تحقق من البريد الإلكتروني</h1>
    <p style="direction: rtl; unicode-bidi: embed; text-align: right;">عزيزي {{ $mailData['name'] }}،</p>
    <p style="direction: rtl; unicode-bidi: embed; text-align: right;">شكرًا لتسجيلك في {{ config('app.name') }}. لإكمال
        تسجيلك، يرجى إدخال كلمة المرور لمرة واحدة (OTP) المقدمة أدناه:</p>
    @include('emails.components.code', ['code' => $mailData['otp']])
    <p style="direction: rtl; unicode-bidi: embed; text-align: right;">إذا لم تطلب هذا التحقق، يرجى تجاهل هذا البريد
        الإلكتروني.</p>
    @include('emails.components.spacer', ['size' => 20])
    <p style="direction: rtl; unicode-bidi: embed; text-align: right; margin:0;">مع أطيب التحيات،</p>
    <p style="direction: rtl; unicode-bidi: embed; text-align: right; margin:0;">فريق {{ config('app.name') }}</p>
@endcomponent
