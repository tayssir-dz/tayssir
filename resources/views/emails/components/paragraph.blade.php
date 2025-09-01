@props(['color' => '#374151', 'margin' => '0 0 16px 0', 'fontSize' => '15px', 'textAlign' => 'right'])
<p
    style="direction: rtl; unicode-bidi: embed; text-align: {{ $textAlign }}; color: {{ $color }}; margin: {{ $margin }}; font-size: {{ $fontSize }}; line-height: 1.6;">
    {{ $slot }}</p>
