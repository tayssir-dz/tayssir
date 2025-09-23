@php($title = config('app.name') . ' - ترحيب وتحقق من البريد الإلكتروني')
@php($preheader = 'مرحباً بك! رمز التحقق: ' . ($mailData['otp'] ?? ''))
@component('emails.layout', compact('title', 'preheader'))
    @component('emails.components.heading')
        مرحبًا بك في {{ config('app.name') }}
    @endcomponent

    @component('emails.components.heading')
        تحقق من البريد الإلكتروني
    @endcomponent

    @component('emails.components.paragraph')
        عزيزي {{ $mailData['name'] }}،
    @endcomponent

    @component('emails.components.paragraph')
        نشكرك على انضمامك إلى {{ config('app.name') }}. يسعدنا أن نرحب بك في مجتمعنا.
    @endcomponent

    @component('emails.components.paragraph')
        يمكنك الآن الاستفادة من جميع الخدمات والميزات المتاحة في منصتنا.
    @endcomponent

    @component('emails.components.paragraph')
        لإكمال تسجيلك، يرجى إدخال كلمة المرور لمرة واحدة (OTP) المقدمة أدناه:
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
