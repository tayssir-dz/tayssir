<?php

namespace App\Notifications\Purchase;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ChargilyPaymentRequestSuccess extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct() {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $name = $notifiable->name;
        return [
            'title' => 'تم استلام طلب الدفع',
            'body' => 'مرحباً ' . $name . '، لقد تم استلام طلب الدفع اليدوي الخاص بك بنجاح. يقوم فريقنا حالياً بمراجعة التفاصيل وسيتم معالجته قريباً. سيتم إشعارك فور اكتمال عملية التحقق.'
        ];
    }
}
