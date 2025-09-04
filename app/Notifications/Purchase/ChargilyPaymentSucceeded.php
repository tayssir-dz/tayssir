<?php

namespace App\Notifications\Purchase;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChargilyPaymentSucceeded extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $subscription_name) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    // public function toMail(object $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //         ->line('The introduction to the notification.')
    //         ->action('Notification Action', url('/'))
    //         ->line('Thank you for using our application!');
    // }

    public function toArray(object $notifiable): array
    {
        $name = $notifiable->name;
        return [
            'title' => 'تمت عملية الدفع بنجاح',
            'body' => 'مرحباً ' . $name . '، لقد تمت عملية الدفع بنجاح لاشتراك ' . $this->subscription_name . ' وأصبح اشتراكك نشطاً الآن. شكراً لاستخدامك خدماتنا!'
        ];
    }
}
