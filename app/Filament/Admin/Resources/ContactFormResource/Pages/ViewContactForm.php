<?php

namespace App\Filament\Admin\Resources\ContactFormResource\Pages;

use App\Filament\Admin\Resources\ContactFormResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewContactForm extends ViewRecord
{
    protected static string $resource = ContactFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\ActionGroup::make([
            // Actions\Action::make('reply')
            //     ->label(__('custom.models.contact_form.actions.reply'))
            //     ->color('primary')
            //     ->url(fn($record) => 'mailto:' . $record->email . '?subject=' . rawurlencode($record->subject))
            //     ->openUrlInNewTab(),
            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->label(__('custom.models.generic.delete')),
            Actions\Action::make('markAsSolved')
                ->icon(fn(): string => $this->record->is_solved ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->label(fn(): string => $this->record->is_solved ? __('custom.models.contact_form.actions.mark_as_unsolved') : __('custom.models.contact_form.actions.mark_as_solved'))
                ->color(fn(): string => $this->record->is_solved ? 'danger' : 'success')
                ->action(function () {
                    $this->record->is_solved = ! $this->record->is_solved;
                    $this->record->save();
                    Notification::make()
                        ->success()
                        ->title(__('The contact form status has been updated successfully.'))
                        ->send();
                }),
            // ]),
        ];
    }
}
