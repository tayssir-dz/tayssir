<?php

use Illuminate\Support\Facades\Route;
use App\Models\Payment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

// Route::get('/emails', function () {
//     return view('emails.preview-wrapper', [
//         'iframeSrc' => route('emails.preview', 'welcome')
//     ]);
// })->name('emails.index');

// Route::get('/emails/{type}', function (string $type) {
//     $mailData = [
//         'name' => 'مستخدم تجريبي',
//         'otp' => '596853',
//     ];

//     $view = match ($type) {
//         'welcome' => 'emails.welcome-mail',
//         'verify' => 'emails.email-verification-mail',
//         'forgot' => 'emails.forgot-password-mail',
//         'change' => 'emails.change-email-mail',
//         default => abort(404)
//     };

//     return view($view, compact('mailData'));
// })->name('emails.preview');

Route::get('/', function () {
    return view('home');
})->name('login');

// Secure route to view payment attachment (super_admin only)
Route::middleware(['web', 'auth', 'role:super_admin'])->group(function () {
    Route::get('/admin/payments/{payment}/attachment', function (Request $request, Payment $payment) {
        $media = $payment->getFirstMedia('attachment');
        abort_unless($media, 404);

        $stream = Storage::disk($media->disk)->readStream($media->getPathRelativeToRoot());
        return response()->stream(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type' => $media->mime_type,
            'Content-Disposition' => 'inline; filename="' . $media->file_name . '"',
        ]);
    })->name('admin.payments.attachment');
});


// Route::get("/testing", function () {
//     // find user with email m_keziz@estin.dz
//     $user = \App\Models\User::where("email", "m_keziz@estin.dz")->first();
//     // notify him with WelcomeNotification (takes 1 arm is name)
//     $user->notify(new \App\Notifications\WelcomeNotification($user->name));
//     return "Notification sent!";
// });



// create endpoint that downloads the file in storage/app/private/database-backup.sqlite
Route::get('/download-backup', function () {
    $filePath = storage_path('app/private/database-backup.sqlite');

    if (!file_exists($filePath)) {
        abort(404, 'File not found.');
    }

    return response()->download($filePath, 'database-backup.sqlite', [
        'Content-Type' => 'application/sqlite',
    ]);
});
