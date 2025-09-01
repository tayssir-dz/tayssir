@php($title = config('app.name') . ' - رسالة ترحيب')
@php($preheader = 'أهلاً بك في ' . config('app.name'))
@component('emails.layout', compact('title', 'preheader'))
    <h1 style="direction: rtl; unicode-bidi: embed; text-align: right;">مرحبًا بك في {{ config('app.name') }}</h1>
    <p style="direction: rtl; unicode-bidi: embed; text-align: right;">عزيزي {{ $mailData['name'] }}،</p>
    <p style="direction: rtl; unicode-bidi: embed; text-align: right;">نشكرك على انضمامك إلى {{ config('app.name') }}. يسعدنا
        أن نرحب بك في
        مجتمعنا.</p>
    <p style="direction: rtl; unicode-bidi: embed; text-align: right;">يمكنك الآن الاستفادة من جميع الخدمات والميزات المتاحة
        في منصتنا.</p>
    @include('emails.components.spacer', ['size' => 20])
    <p style="direction: rtl; unicode-bidi: embed; text-align: right; margin:0;">مع أطيب التحيات،</p>
    <p style="direction: rtl; unicode-bidi: embed; text-align: right; margin:0;">فريق {{ config('app.name') }}</p>
@endcomponent
