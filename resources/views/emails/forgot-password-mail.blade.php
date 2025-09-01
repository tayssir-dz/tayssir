@php($title = config('app.name') . ' - نسيت كلمة المرور')
@php($preheader = 'طلب إعادة تعيين كلمة المرور - رمز: ' . ($mailData['otp'] ?? ''))
@component('emails.layout', compact('title', 'preheader'))
    <h1>نسيت كلمة المرور</h1>
    <p>عزيزي {{ $mailData['name'] }}،</p>
    <p>لقد تلقينا طلبًا لإعادة تعيين كلمة المرور الخاصة بك في {{ config('app.name') }}. للمتابعة في إعادة تعيين كلمة المرور،
        يرجى إدخال كلمة المرور لمرة واحدة (OTP) المقدمة أدناه:</p>
    @include('emails.components.code', ['code' => $mailData['otp']])
    <p>إذا لم تطلب إعادة تعيين كلمة المرور، يرجى تجاهل هذا البريد الإلكتروني.</p>
    @include('emails.components.spacer', ['size' => 20])
    <p style="margin:0;">مع أطيب التحيات،</p>
    <p style="margin:0;">فريق {{ config('app.name') }}</p>
@endcomponent
