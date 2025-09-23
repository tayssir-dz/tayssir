<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8" />
    <title>{{ $title ?? config('app.name') }}</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <style>
        body {
            background: #efeef1;
            margin: 0;
            padding: 0;
            font-family: Helvetica, Arial, sans-serif;
            direction: rtl;
            text-align: right;
        }

        table {
            border-collapse: collapse;
        }

        img {
            border: 0;
            outline: none;
            text-decoration: none;
        }

        .container-outer {
            max-width: 580px;
            margin: 30px auto;
            background: #ffffff;
        }

        .header-block {
            padding: 30px 30px 10px;
        }

        .divider-accent {
            width: 100%;
        }

        .content {
            padding: 5px 20px 30px;
        }

        h1 {
            font-size: 22px;
            margin: 0 0 20px;
            font-weight: 700;
            color: #1f2937;
            line-height: 1.3;
        }

        p {
            font-size: 15px;
            line-height: 1.6;
            margin: 16px 0;
            color: #374151;
        }

        a {
            color: #00C4F6;
            text-decoration: underline;
        }

        .btn {
            display: inline-block;
            background: #00C4F6;
            color: #fff !important;
            text-decoration: none;
            font-size: 15px;
            padding: 16px 32px;
            border-radius: 10px;
            font-weight: 600;
        }

        .code {
            font-size: 36px;
            font-weight: 700;
            line-height: 24px;
            margin: 10px 0;
            text-align: center;
            letter-spacing: 3px;
        }

        .footer-note {
            font-size: 12px;
            line-height: 20px;
            color: #706a7b;
            text-align: center;
            margin: 24px 0 0;
        }

        .muted {
            color: #706a7b;
        }

        .spacer {
            line-height: 1px;
            font-size: 1px;
            height: 20px;
        }

        .preheader {
            display: none;
            overflow: hidden;
            line-height: 1px;
            opacity: 0;
            max-height: 0;
            max-width: 0;
        }

        @media only screen and (max-width:620px) {
            .container-outer {
                width: 100% !important;
            }

            .header-block {
                padding: 24px 20px 8px !important;
            }

            .content {
                padding: 5px 16px 24px !important;
            }
        }
    </style>
</head>

<body>
    <table role="presentation" width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td style="background:#efeef1;">
                <div class="preheader">{{ $preheader ?? '' }}</div>
                <table role="presentation" width="100%" align="center" cellpadding="0" cellspacing="0" border="0"
                    class="container-outer">
                    <tr>
                        <td>
                            @include('emails.components.header')
                            @include('emails.components.divider-accent')
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td class="content" dir="rtl" style="text-align: right;">
                                        {{ $slot }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                <tr>
                    <td>
                        @include('emails.components.footer', ['margin' => '0'])
                    </td>
                </tr>
                </table>
                @isset($legal)
                    <p class="footer-note">{!! $legal !!}</p>
                @endisset
            </td>
        </tr>
    </table>
</body>

</html>
