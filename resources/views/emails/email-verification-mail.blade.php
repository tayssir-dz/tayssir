@php($title = config('app.name') . ' - تحقق من البريد الإلكتروني')
@php($preheader = 'رمز التحقق من البريد الإلكتروني الخاص بك: ' . ($mailData['otp'] ?? ''))
@component('emails.layout', compact('title', 'preheader'))
    @component('emails.components.heading')
        تحقق من البريد الإلكتروني
    @endcomponent
    @component('emails.components.paragraph')
        عزيزي {{ $mailData['name'] }}،
    @endcomponent
    @component('emails.components.paragraph')
        شكرًا لتسجيلك في {{ config('app.name') }}. لإكمال تسجيلك، يرجى إدخال كلمة المرور لمرة واحدة (OTP) المقدمة أدناه:
    @endcomponent
    @include('emails.components.code', ['code' => $mailData['otp']])
    @component('emails.components.paragraph')
        إذا لم تطلب هذا التحقق، يرجى تجاهل هذا البريد الإلكتروني.
    @endcomponent
    @include('emails.components.spacer', ['size' => 20])
    @component('emails.components.paragraph', ['margin' => '0'])
        مع أطيب التحيات،
    @endcomponent
    @component('emails.components.paragraph', ['margin' => '0'])
        فريق {{ config('app.name') }}
    @endcomponent
@endcomponent
