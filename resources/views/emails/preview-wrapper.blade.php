<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8" />
    <title>Email Preview - {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        body {
            background: #0f172a;
            margin: 0;
            font-family: system-ui, sans-serif;
            color: #f1f5f9;
        }

        .grid {
            display: grid;
            place-items: center;
            min-height: 100dvh;
            padding: 40px;
        }

        .frame {
            background: #fff;
            color: #000;
            width: 100%;
            max-width: 760px;
            border-radius: 14px;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, .3);
            overflow: hidden;
        }

        .frame iframe {
            width: 100%;
            height: 80vh;
            border: 0;
            background: #fff;
        }

        header {
            padding: 18px 24px;
            background: #00C4F6;
            color: #fff;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header a {
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            margin-inline-start: 16px;
        }

        .links {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .links a {
            background: rgba(255, 255, 255, .15);
            padding: 6px 10px;
            border-radius: 6px;
            backdrop-filter: blur(4px);
        }
    </style>
</head>

<body>
    <div class="grid">
        <div class="frame">
            <header>
                <div>{{ config('app.name') }} Email Preview</div>
                <nav class="links">
                    <a href="{{ route('emails.preview', 'welcome') }}">Welcome</a>
                    <a href="{{ route('emails.preview', 'verify') }}">Verify</a>
                    <a href="{{ route('emails.preview', 'forgot') }}">Forgot</a>
                    <a href="{{ route('emails.preview', 'change') }}">Change Email</a>
                </nav>
            </header>
            <iframe src="{{ $iframeSrc }}" title="Email"></iframe>
        </div>
    </div>
</body>

</html>
