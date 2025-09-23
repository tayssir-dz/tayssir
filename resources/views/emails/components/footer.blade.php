@php($platformSettings = app(\App\Settings\PlatformSettings::class))
@php($socials = [
    'instagram' => ['active' => $platformSettings->instagram_active ?? false, 'icon' => asset('socials/instagram.png')],
    'facebook' => ['active' => $platformSettings->facebook_active ?? false, 'icon' => asset('socials/facebook.png')],
    'tiktok' => ['active' => $platformSettings->tiktok_active ?? false, 'icon' => asset('socials/tiktok.png')],
    'youtube' => ['active' => $platformSettings->youtube_active ?? false, 'icon' => asset('socials/youtube.png')],
    'linkedin' => ['active' => $platformSettings->linkedin_active ?? false, 'icon' => asset('socials/linkedin.png')],
])

@php($anyActive = collect($socials)->contains(fn($s) => $s['active']))
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="footer">
    <tr>
        <td style="padding:10px; text-align:center; background:#ffffff;">
            {{-- <p style="margin:0 0 12px; font-size:14px; line-height:24px; color:#374151;">
                {{ $footerText ?? 'لن نطلب منك أبدًا مشاركة كلمة المرور أو البيانات الحساسة عبر البريد الإلكتروني.' }}
            </p> --}}

            @if($anyActive)
                <div style="margin-top:12px;">
                    @foreach($socials as $key => $data)
                        @if($data['active'])
                            <a href="{{ route('social.redirect', $key) }}" target="_blank" rel="noopener" style="display:inline-block;margin:0 6px;">
                                <img src="{{ $data['icon'] }}" alt="{{ ucfirst($key) }}" width="32" height="32" style="display:block;width:32px;height:32px;">
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </td>
    </tr>
</table>
