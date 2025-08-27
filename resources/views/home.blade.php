<!DOCTYPE html>
<html data-theme="winter" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

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
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles (tailwind and daisy ui) -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-base-300">
    <div class="flex items-center justify-center h-screen w-full max-w-2xl mx-auto px-3">
        <div class="flex flex-col gap-8 justify-center items-center card card-body card-bordered bg-base-200">
            <!-- Logo and App Name -->
            <div class="flex flex-col items-center gap-4">
                {{-- <x-logo /> --}}
                <h1 class="text-4xl font-bold text-center">{{ config('app.name') }}</h1>
            </div>

            <!-- Buttons -->
            <div class="flex flex-col gap-3 w-full max-w-sm">
                <a href="/dashboard" class="btn text-white bg-cyan-500">
                    Admin Space
                </a>
                <a href="/promoter"
                    class="btn text-white bg-pink-500 tooltip tooltip-top tooltip-primary flex justify-center items-center"
                    data-tip="{{ config('app.url') }}/promoter">
                    Promoter Space
                </a>
                <a href="/swagger/documentation"
                    class="btn text-white bg-lime-600 tooltip tooltip-top tooltip-primary flex justify-center items-center"
                    data-tip="Old Swagger docs - contains both v1, v2 APIs, not recommanded to use">
                    Old Swagger
                </a>
                <a href="/docs/v1"
                    class="btn btn-neutral tooltip tooltip-top tooltip-primary flex justify-center items-center"
                    data-tip="Same APIs, no change">
                    New Documentation (v1)
                </a>
                <a href="/docs/v2"
                    class="btn btn-neutral tooltip tooltip-top tooltip-primary flex justify-center items-center"
                    data-tip="All APIs included in the v2 that Require Auth - Requires email verification">
                    New Documentation (v2)
                </a>
                <a href="/docs/default"
                    class="btn btn-neutral tooltip tooltip-top tooltip-primary flex justify-center items-center"
                    data-tip="ignore these for now">
                    New Documentation (default)
                </a>
            </div>
        </div>
    </div>
</body>

</html>
