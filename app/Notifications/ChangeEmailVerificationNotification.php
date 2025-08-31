<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\View;
use Illuminate\Notifications\Notification;

class ChangeEmailVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $otp) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailData = [
            'otp' => $this->otp,
            'name' => $notifiable->name,
        ];

        return (new MailMessage)
            ->subject(config('app.name') . ' - تغيير البريد الإلكتروني')
            ->view('emails.change-email-mail', compact('mailData'));
    }
}
