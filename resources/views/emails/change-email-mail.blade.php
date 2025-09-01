@php($title = config('app.name') . ' - تغيير البريد الإلكتروني')
@php($preheader = 'رمز تغيير البريد الإلكتروني: ' . ($mailData['otp'] ?? ''))
@component('emails.layout', compact('title', 'preheader'))
    @component('emails.components.heading')
        تغيير البريد الإلكتروني
    @endcomponent
    @component('emails.components.paragraph')
        عزيزي {{ $mailData['name'] }}،
    @endcomponent
    @component('emails.components.paragraph')
        لقد تلقينا طلبًا لتغيير بريدك الإلكتروني في {{ config('app.name') }}. لإكمال العملية، يرجى إدخال كلمة المرور لمرة واحدة
        (OTP) المقدمة أدناه:
    @endcomponent
    @include('emails.components.code', ['code' => $mailData['otp']])
    @component('emails.components.paragraph')
        إذا لم تطلب هذا التغيير، يرجى تجاهل هذا البريد الإلكتروني وتأمين حسابك.
    @endcomponent
    @include('emails.components.spacer', ['size' => 20])
    @component('emails.components.paragraph', ['margin' => '0'])
        مع أطيب التحيات،
    @endcomponent
    @component('emails.components.paragraph', ['margin' => '0'])
        فريق {{ config('app.name') }}
    @endcomponent
@endcomponent
