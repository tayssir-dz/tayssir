<?php

namespace App\Services\Notification;

use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

class AdminNotifications
{
    public static function newManualPayment($user, string $subscriptionName, string $invoiceUrl = '#')
    {
        $admins = User::admins()->get();
        foreach ($admins as $admin) {
            self::sendNewPaymentNotification($admin, $user, $subscriptionName, $invoiceUrl);
        }
    }

    public static function newChargilyPayment($user, string $subscriptionName, string $invoiceUrl = '#')
    {
        $admins = User::admins()->get();
        foreach ($admins as $admin) {
            self::sendNewChargilyPaymentNotification($admin, $user, $subscriptionName, $invoiceUrl);
        }
    }
    protected static function sendNewPaymentNotification(User $admin, User $user, string $subscriptionName, string $invoiceUrl)
    {
        Notification::make()
            ->title('دفعة جديدة')
            ->success()
            ->icon("heroicon-o-document-currency-dollar")
            ->body(__('لديك دفعة يدوية جديدة من المستخدم (:user) للاشتراك (:subscription) مع إثبات دفع مرفوع يتطلب المراجعة', ['user' => $user->name, 'subscription' => $subscriptionName], app()->getLocale()))
            ->actions([
                Action::make('view-invoice')
                    ->label(__("عرض التفاصيل"))
                    ->button()
                    ->color('primary')
                    ->url($invoiceUrl)
                    ->openUrlInNewTab(),
                Action::make('view-user')
                    ->label(__("عرض المستخدم"))
                    ->url(route('filament.dashboard.resources.users.edit', $user->id))
                    ->openUrlInNewTab(),
                // Action::make('mark-as-read')
                //     ->label(__("وضع كمقروء"))
                //     ->icon('heroicon-o-check')
                //     ->markAsRead()
                //     ->iconButton()
                //     ->color('success'),
            ])
            ->sendToDatabase($admin);
    }

    protected static function sendNewChargilyPaymentNotification(User $admin, User $user, string $subscriptionName, string $invoiceUrl)
    {
        Notification::make()
            ->title('دفعة شارجيلي جديدة')
            ->success()
            ->icon('heroicon-o-credit-card')
            ->body(__('لديك دفعة جديدة عبر شارجيلي من المستخدم (:user) للاشتراك (:subscription)', ['user' => $user->name, 'subscription' => $subscriptionName], app()->getLocale()))
            ->actions([
                Action::make('view-invoice')
                    ->label(__('عرض التفاصيل'))
                    ->button()
                    ->color('primary')
                    ->url($invoiceUrl)
                    ->openUrlInNewTab(),
                Action::make('view-user')
                    ->label(__('عرض المستخدم'))
                    ->url(route('filament.dashboard.resources.users.edit', $user->id))
                    ->openUrlInNewTab(),
            ])
            ->sendToDatabase($admin);
    }
}
