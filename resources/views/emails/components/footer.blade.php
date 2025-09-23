@php($platformSettings = app(\App\Settings\PlatformSettings::class))
@php($socials = [
    'instagram' => ['active' => $platformSettings->instagram_active ?? false, 'icon' => asset('socials/instagram.png')],
    'facebook' => ['active' => $platformSettings->facebook_active ?? false, 'icon' => asset('socials/facebook.png')],
    'tiktok' => ['active' => $platformSettings->tiktok_active ?? false, 'icon' => asset('socials/tiktok.png')],
    'youtube' => ['active' => $platformSettings->youtube_active ?? false, 'icon' => asset('socials/youtube.png')],
    'linkedin' => ['active' => $platformSettings->linkedin_active ?? false, 'icon' => asset('socials/linkedin.png')],
])
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="footer">
    <tr>
        <td class="section" style="text-align:{{ app()->getLocale() === 'ar' ? 'right':'left' }};">
            <p style="margin:0;font-size:14px;line-height:24px;">
                {{ $footerText ?? 'لن نطلب منك أبدًا مشاركة كلمة المرور أو البيانات الحساسة عبر البريد الإلكتروني.' }}
            </p>
            @php($anyActive = collect($socials)->contains(fn($s) => $s['active']))
            @if($anyActive)
                <div style="margin-top:12px;">
                    @foreach($socials as $key => $data)
                        @if($data['active'])
                            <a href="{{ route('social.redirect', $key) }}" target="_blank" rel="noopener" style="display:inline-block;margin:0 4px;">
                                <img src="{{ $data['icon'] }}" alt="{{ ucfirst($key) }}" width="32" height="32" style="display:block;border:0;outline:none;text-decoration:none;width:32px;height:32px;">
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </td>
    </tr>
</table>
