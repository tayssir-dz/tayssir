<?php

namespace App\Filament\Admin\Resources\QuestionReportResource\Pages;

use App\Filament\Admin\Resources\ChapterResource;
use App\Filament\Admin\Resources\QuestionReportResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewQuestionReport extends ViewRecord
{
    protected static string $resource = QuestionReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make("goToChapter")
                ->label(__("custom.models.question_report.question"))
                ->icon('heroicon-o-question-mark-circle')
                ->url(fn() => ChapterResource::getUrl('edit', ['record' => $this->record->question->chapter()->first()]))
                ->color("info"),
            Actions\DeleteAction::make()
                ->label(__('custom.models.generic.delete')),
            Actions\ActionGroup::make([
                Actions\Action::make('markAsSolved')
                    ->icon(fn(): string => $this->record->is_solved ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->label(fn(): string => $this->record->is_solved ? __('custom.models.question_report.actions.mark_as_unsolved') : __('custom.models.question_report.actions.mark_as_solved'))
                    ->color(fn(): string => $this->record->is_solved ? 'danger' : 'success')
                    ->action(function () {
                        $this->record->is_solved = ! $this->record->is_solved;
                        $this->record->save();
                        Notification::make()
                            ->success()
                            ->title(__('The question report status has been updated successfully.'))
                            ->send();
                    }),
                Actions\Action::make('markAsContacted')
                    ->icon(fn(): string => $this->record->is_contacted ? 'heroicon-o-phone-x-mark' : 'heroicon-o-phone')
                    ->label(fn(): string => $this->record->is_contacted ? __('custom.models.question_report.actions.mark_as_not_contacted') : __('custom.models.question_report.actions.mark_as_contacted'))
                    ->color(fn(): string => $this->record->is_contacted ? 'danger' : 'info')
                    ->action(function () {
                        $this->record->is_contacted = ! $this->record->is_contacted;
                        $this->record->save();
                        Notification::make()
                            ->success()
                            ->title(__('The question report contact status has been updated successfully.'))
                            ->send();
                    }),
            ]),
        ];
    }
}
