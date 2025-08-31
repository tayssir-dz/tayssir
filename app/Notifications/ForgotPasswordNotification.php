<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ForgotPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $otp) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mailData = [
            'otp' => $this->otp,
            'name' => $notifiable->name,
        ];

        return (new MailMessage)
            ->subject(config('app.name') . ' - إستعادة كلمة المرور')
            ->view('emails.forgot-password-mail', compact('mailData'));
    }
}
