<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Howdu\FilamentRecordSwitcher\Filament\Concerns\HasRecordSwitcher;

class EditUser extends EditRecord
{
    use HasRecordSwitcher;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        // $routeName = static::getRouteName();
        // $currentRoute = request()->route()->getName();
        $record = $this->getRecord();
        return [
            Actions\Action::make('sendCustomNotification')
                ->label(__('custom.models.user.actions.send_custom_notification'))
                ->icon('heroicon-o-bell')
                // form for title and body
                ->form([
                    \Filament\Forms\Components\TextInput::make('title')
                        ->label(__('custom.models.user.actions.send_custom_notification.title'))
                        ->required()
                        ->maxLength(255),
                    \Filament\Forms\Components\Textarea::make('body')
                        ->label(__('custom.models.user.actions.send_custom_notification.body'))
                        ->required()
                        ->maxLength(65535),
                ])
                ->action(function (array $data) use ($record): void {
                    $record->notify(new \App\Notifications\CustomUserNotification($data['title'], $data['body']));
                    Notification::make()
                        ->title(__('custom.models.user.notices.custom_notification_sent'))
                        ->success()
                        ->send();
                })
                ->color(color: 'warning'),
            Actions\DeleteAction::make(),
        ];
    }
}
