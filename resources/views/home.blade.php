<!DOCTYPE html>
<html data-theme="winter" lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Tayssir</title>

    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles (tailwind and daisy ui) -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-base-200">
    <div class="hero min-h-screen">
        <div class="hero-content text-center w-full max-w-lg">
            <div class="w-full">
                <!-- Logo -->
                <div class="flex flex-col items-center gap-4 mb-8 scale-75">
                    <x-logo />
                </div>

                <!-- Buttons -->
                <div class="flex flex-col gap-4">
                    <a href="/dashboard" class="btn btn-primary">
                        Admin Space
                    </a>
                    <a href="/promoter"
                        class="btn btn-accent text-white tooltip tooltip-top tooltip-accent flex justify-center items-center"
                        data-tip="{{ config('app.url') }}/promoter">
                        Promoter Space
                    </a>
                    <a href="/swagger/documentation"
                        class="btn btn-secondary tooltip tooltip-top tooltip-primary flex justify-center items-center"
                        {{-- data-tip="Old Swagger docs - contains both v1, v2 APIs, not recommanded to use" --}}>
                        Old Swagger
                    </a>
                    <a href="/docs/v1"
                        class="btn btn-neutral tooltip tooltip-top tooltip-primary flex justify-center items-center"
                        {{-- data-tip="Same APIs, no change" --}}>
                        Documentation (v1)
                    </a>
                    <a href="/docs/v2"
                        class="btn btn-neutral tooltip tooltip-top tooltip-primary flex justify-center items-center"
                        {{-- data-tip="All APIs included in the v2 that Require Auth - Requires email verification" --}}>
                        Documentation (v2)
                    </a>
                    <a href="/health" class="btn btn-accent text-white">
                        Health
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
