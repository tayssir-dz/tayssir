<?php

namespace App\Notifications\Purchase;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ManualPaymentFailed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ?string $rejection_reason = null, public string $subscription_name = '') {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $name = $notifiable->name;
        $reason = trim((string) $this->rejection_reason);
        $base = 'مرحباً ' . $name . '، لقد فشلت عملية الدفع اليدوي لاشتراك ' . $this->subscription_name . '. ';
        $message = $reason === ''
            ? $base . 'يرجى المحاولة مرة أخرى أو التواصل معنا للحصول على المساعدة.'
            : $base . 'سبب الرفض: ' . $reason . '. يرجى مراجعة السبب والمحاولة مرة أخرى أو التواصل معنا.';

        return [
            'title' => 'فشلت عملية الدفع',
            'body' => $message,
        ];
    }
}
