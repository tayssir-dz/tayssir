@php($title = config('app.name') . ' - نسيت كلمة المرور')
@php($preheader = 'طلب إعادة تعيين كلمة المرور - رمز: ' . ($mailData['otp'] ?? ''))
@component('emails.layout', compact('title', 'preheader'))
    @component('emails.components.heading')
        نسيت كلمة المرور
    @endcomponent
    @component('emails.components.paragraph')
        عزيزي {{ $mailData['name'] }}،
    @endcomponent
    @component('emails.components.paragraph')
        لقد تلقينا طلبًا لإعادة تعيين كلمة المرور الخاصة بك في {{ config('app.name') }}. للمتابعة في إعادة تعيين كلمة المرور،
        يرجى إدخال كلمة المرور لمرة واحدة (OTP) المقدمة أدناه:
    @endcomponent
    @include('emails.components.code', ['code' => $mailData['otp']])
    @component('emails.components.paragraph')
        إذا لم تطلب إعادة تعيين كلمة المرور، يرجى تجاهل هذا البريد الإلكتروني.
    @endcomponent
    @include('emails.components.spacer', ['size' => 20])
    @component('emails.components.paragraph', ['margin' => '0'])
        مع أطيب التحيات،
    @endcomponent
    @component('emails.components.paragraph', ['margin' => '0'])
        فريق {{ config('app.name') }}
    @endcomponent
@endcomponent
