@php($title = config('app.name') . ' - رسالة ترحيب')
@php($preheader = 'أهلاً بك في ' . config('app.name'))
@component('emails.layout', compact('title', 'preheader'))
    <h1>مرحبًا بك في {{ config('app.name') }}</h1>
    <p>عزيزي {{ $mailData['name'] }}،</p>
    <p>نشكرك على انضمامك إلى {{ config('app.name') }}. يسعدنا أن نرحب بك في مجتمعنا.</p>
    <p>يمكنك الآن الاستفادة من جميع الخدمات والميزات المتاحة في منصتنا.</p>
    @include('emails.components.spacer', ['size' => 20])
    <p style="margin:0;">مع أطيب التحيات،</p>
    <p style="margin:0;">فريق {{ config('app.name') }}</p>
@endcomponent
