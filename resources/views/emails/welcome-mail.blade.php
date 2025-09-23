@php($title = config('app.name') . ' - رسالة ترحيب')
@php($preheader = 'أهلاً بك في ' . config('app.name'))
@component('emails.layout', compact('title', 'preheader'))
    @component('emails.components.heading')
        مرحبًا بك في {{ config('app.name') }}
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

    @include('emails.components.spacer', ['size' => 20])

    @component('emails.components.paragraph', ['margin' => '0'])
        مع أطيب التحيات،
    @endcomponent

    @component('emails.components.paragraph', ['margin' => '0'])
        فريق {{ config('app.name') }}
    @endcomponent
@endcomponent
