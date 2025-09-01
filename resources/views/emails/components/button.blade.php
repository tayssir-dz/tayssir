@props(['url', 'color' => '#00C4F6', 'textColor' => '#ffffff'])
<table role="presentation" align="center" cellpadding="0" cellspacing="0" border="0" style="margin:24px auto;">
    <tr>
        <td align="center" style="border-radius:10px;background:{{ $color }};">
            <a href="{{ $url }}" target="_blank"
                style="background:{{ $color }};color:{{ $textColor }} !important;text-decoration:none;display:inline-block;padding:16px 32px;border-radius:10px;font-weight:600;font-size:15px;line-height:20px;border:0;box-shadow:0 2px 4px rgba(0,196,246,0.2);">{{ $slot }}</a>
        </td>
    </tr>
</table>
