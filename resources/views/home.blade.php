<!DOCTYPE html>
<html data-theme="winter" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Tayssir</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles (tailwind and daisy ui) -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-base-300">
    {{-- big text in the center of the screen with the APP_NAME from env inside of it --}}
    <div class="flex items-center justify-center h-screen w-full max-w-4xl mx-auto px-3">
        <div class="flex flex-col gap-6 justify-center items-center card card-body card-bordered bg-base-200">
            <x-logo />
            <p class="text-center">This app provides a comprehensive
                platform
                for
                baccalaureate students to practice quizzes
                and enhance their knowledge. With a user-friendly interface and a wide range of subjects to choose from,
                students can test their understanding and track their progress. Join now and boost your exam preparation
                with our interactive quizzes!The app provides a comprehensive platform for baccalaureate students to
                practice quizzes and enhance their knowledge. With a user-friendly interface and a wide range of
                subjects to
                choose from, students can test their understanding and track their progress. Join now and boost your
                exam
                preparation with our interactive quizzes!
                <span class="font-bold">(the previous text is auto generated)</span>
            </p>
            <div class="w-full flex justify-end items-center gap-4">
                <a href="/swagger/documentation" class="btn btn-primary">
                    go to swagger
                </a>
                <a href="/dashboard" class="btn btn-primary">
                    go to dashboard
                </a>
            </div>
        </div>
    </div>
</body>

</html>
