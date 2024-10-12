<!DOCTYPE html>
<html data-theme="winter" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>TAYSSIR</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles (tailwind and daisy ui) -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="w-full h-screen flex justify-center items-center text-center">
    <select class="select select-bordered w-full max-w-xs">
        <option disabled selected>choose wilaya</option>
        @foreach (wilayas() as $id => $wilaya)
            <option value="{{ $id }}">{{ $wilaya }}</option>
        @endforeach
    </select>
    <select class="select select-bordered w-full max-w-xs">
        @foreach (communes($wilaya_id = 40) as $id => $commune)
            <option value="{{ $id }}">{{ $commune }}</option>
        @endforeach
    </select>
</body>
