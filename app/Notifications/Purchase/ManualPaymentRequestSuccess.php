<?php

namespace App\Notifications\Purchase;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ManualPaymentRequestSuccess extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct() {}


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
            'title' => 'تم استلام طلب الدفع',
            'body' => 'مرحباً ' . $name . '، لقد تم استلام طلب الدفع اليدوي الخاص بك بنجاح. يقوم فريقنا حالياً بمراجعة التفاصيل وسيتم معالجته قريباً. سيتم إشعارك فور اكتمال عملية التحقق.'
        ];
    }
}
