@php($title = config('app.name') . ' - تغيير البريد الإلكتروني')
@php($preheader = 'رمز تغيير البريد الإلكتروني: ' . ($mailData['otp'] ?? ''))
@component('emails.layout', compact('title', 'preheader'))
    <h1>تغيير البريد الإلكتروني</h1>
    <p>عزيزي {{ $mailData['name'] }}،</p>
    <p>لقد تلقينا طلبًا لتغيير بريدك الإلكتروني في {{ config('app.name') }}. لإكمال العملية، يرجى إدخال كلمة المرور لمرة
        واحدة (OTP) المقدمة أدناه:</p>
    @include('emails.components.code', ['code' => $mailData['otp']])
    <p>إذا لم تطلب هذا التغيير، يرجى تجاهل هذا البريد الإلكتروني وتأمين حسابك.</p>
    @include('emails.components.spacer', ['size' => 20])
    <p style="margin:0;">مع أطيب التحيات،</p>
    <p style="margin:0;">فريق {{ config('app.name') }}</p>
@endcomponent
