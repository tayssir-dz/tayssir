<?php

namespace App\Filament\Admin\Resources\QuestionReportResource\Pages;

use App\Filament\Admin\Resources\QuestionReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewQuestionReport extends ViewRecord
{
    protected static string $resource = QuestionReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\Action::make('markAsRead')
                    ->label(__('models.question_report.actions.mark_as_read'))
                    ->visible(fn($record) => ! $record->is_read)
                    ->action(fn($record) => $record->markAsRead())
                    ->color('success')
                    ->requiresConfirmation(),
                Actions\Action::make('markAsUnread')
                    ->label(__('models.question_report.actions.mark_as_unread'))
                    ->visible(fn($record) => $record->is_read)
                    ->action(fn($record) => $record->markAsUnread())
                    ->color('warning')
                    ->requiresConfirmation(),
                Actions\DeleteAction::make()
                    ->label(__('models.generic.delete')),
            ]),
        ];
    }
}
