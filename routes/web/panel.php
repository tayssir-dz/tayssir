<?php

use Illuminate\Support\Facades\Route;
use App\Models\Payment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;


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


    //     Route::get('database/backup', function (Request $request) {
    //         $path = 'private/database-backup.sqlite';

    //         if (!Storage::disk('local')->exists($path)) {
    //             abort(404);
    //         }

    //         $stream = Storage::disk('local')->readStream($path);
    //         return response()->stream(function () use ($stream) {
    //             fpassthru($stream);
    //         }, 200, [
    //             'Content-Type' => 'application/octet-stream',
    //             'Content-Disposition' => 'attachment; filename="databasee.sqlite"',
    //         ]);
    //     })->name('api.database.backup.download');
});
