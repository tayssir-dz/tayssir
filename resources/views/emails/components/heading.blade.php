@props(['level' => 1, 'color' => '#1f2937'])
@php
    $tag = 'h' . $level;
    $fontSize = match ($level) {
        1 => '24px',
        2 => '20px',
        3 => '18px',
        4 => '16px',
        5 => '14px',
        6 => '12px',
        default => '24px',
    };
@endphp
<{{ $tag }}
    style="direction: rtl; unicode-bidi: embed; text-align: right; font-size: {{ $fontSize }}; color: {{ $color }}; margin: 0 0 16px 0; font-weight: 600;">
    {{ $slot }}</{{ $tag }}>
