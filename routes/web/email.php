<?php

use Illuminate\Support\Facades\Route;

Route::get('/emails', function () {
    return view('emails.preview-wrapper', [
        'iframeSrc' => route('emails.preview', 'welcome')
    ]);
})->name('emails.index');

Route::get('/emails/{type}', function (string $type) {
    $mailData = [
        'name' => 'مستخدم تجريبي',
        'otp' => '596853',
    ];

    $view = match ($type) {
        'welcome' => 'emails.welcome-mail',
        'verify' => 'emails.email-verification-mail',
        'forgot' => 'emails.forgot-password-mail',
        'change' => 'emails.change-email-mail',
        default => abort(404)
    };

    return view($view, compact('mailData'));
})->name('emails.preview');
