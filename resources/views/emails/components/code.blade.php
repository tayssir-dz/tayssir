@props(['code'])
<table role="presentation" align="center" width="100%" cellpadding="0" cellspacing="0" border="0"
    style="margin:24px 0 28px;">
    <tr>
        <td align="center">
            <p style="font-size:15px;line-height:20px;margin:0 0 16px;font-weight:600;text-align:center;color:#374151;">
                رمز التحقق</p>
            <table role="presentation" align="center" width="100%" cellpadding="0" cellspacing="0" border="0"
                style="background:#f5f4f5;border-radius:8px;margin-bottom:20px;">
                <tr>
                    <td style="padding:32px 10px;">
                        <p dir="ltr"
                            style="font-size:32px;line-height:32px;text-align:center;vertical-align:middle;margin:0;font-weight:700;font-family:system-ui,sans-serif;color:#1f2937;letter-spacing:4px;">
                            {{ $code }}</p>
                    </td>
                </tr>
            </table>
            <p style="font-size:13px;line-height:18px;margin:0;color:#6b7280;text-align:center;">(صالح لمدة 10 دقائق)
            </p>
            <div style="display:none;" aria-label="OTP Code: {{ $code }}"></div>
        </td>
    </tr>
</table>
