<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $otp) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mailData = [
            'otp' => $this->otp,
            'name' => $notifiable->name,
        ];

        return (new MailMessage)
            ->subject('شكرًا لانضمامك إلى ' . config('app.name') . '! تحقق من حسابك بهذا الرمز')
            ->view('emails.email-verification-mail', compact('mailData'));
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'مرحبا بك في ' . config('app.name'),
            'body' => 'نرحب بانضمامك إلى ' . config('app.name') . ' . نتمنى لك تجربة ممتعة.',
        ];
    }
}
