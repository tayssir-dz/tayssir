<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\AdminNavigation;
use App\Filament\Admin\Resources\QuestionReportResource\Pages;
use App\Filament\Admin\Resources\QuestionReportResource\RelationManagers;
use App\Models\QuestionReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuestionReportResource extends Resource
{
    protected static ?string $model = QuestionReport::class;
    protected static ?int $navigationSort = AdminNavigation::QUESTION_REPORT_RESOURCE['sort'];
    protected static ?string $navigationIcon = AdminNavigation::QUESTION_REPORT_RESOURCE['icon'];
    public static function getNavigationGroup(): ?string
    {
        return  __(AdminNavigation::QUESTION_REPORT_RESOURCE['group']);
    }

    public static function getModelLabel(): string
    {
        return __('custom.models.question_report');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.question_reports');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn($record) => static::getUrl('view', ['record' => $record]))
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['user', 'question']))
            ->columns([
                TextColumn::make('is_read')
                    ->label(__('custom.models.question_report.read_status'))
                    ->formatStateUsing(fn($state) => $state ? __('custom.models.question_report.read') : __('custom.models.question_report.unread'))
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'warning')
                    ->toggleable(),
                TextColumn::make('description')
                    ->label(__('custom.models.question_report.description'))
                    ->limit(40)
                    ->placeholder(__('custom.models.generic.empty'))
                    ->wrap(),
                TextColumn::make('user.name')
                    ->label(__('custom.models.question_report.user'))
                    ->placeholder(__('custom.models.generic.empty'))
                    ->toggleable(),
                TextColumn::make('question.chapter_name')
                    ->label(__('custom.models.question_report.chapter'))
                    ->placeholder(__('custom.models.generic.empty'))
                    ->toggleable(),
                TextColumn::make('question.question')
                    ->label(__('custom.models.question_report.question'))
                    ->limit(50)
                    ->wrap()
                    ->placeholder(__('custom.models.generic.empty')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('markAsRead')
                        ->label(__('custom.models.question_report.actions.mark_as_read'))
                        ->visible(fn($record) => ! $record->is_read)
                        ->action(fn($record) => $record->markAsRead())
                        ->color('success')
                        ->requiresConfirmation(),
                    Tables\Actions\Action::make('markAsUnread')
                        ->label(__('custom.models.question_report.actions.mark_as_unread'))
                        ->visible(fn($record) => $record->is_read)
                        ->action(fn($record) => $record->markAsUnread())
                        ->color('warning')
                        ->requiresConfirmation(),
                    Tables\Actions\DeleteAction::make()
                        ->label(__('custom.models.generic.delete')),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('custom.models.generic.delete_selected')),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestionReports::route('/'),
            'view' => Pages\ViewQuestionReport::route('/{record}'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfoSection::make(__('custom.models.question_report.sections.report_info'))
                    ->schema([
                        TextEntry::make('is_read')
                            ->label(__('custom.models.question_report.read_status'))
                            ->formatStateUsing(fn($state) => $state ? __('custom.models.question_report.read') : __('custom.models.question_report.unread')),
                        TextEntry::make('description')
                            ->label(__('custom.models.question_report.description'))
                            ->placeholder(__('custom.models.generic.empty'))
                            ->columnSpanFull(),
                    ]),
                InfoSection::make(__('custom.models.question_report.sections.references'))
                    ->schema([
                        TextEntry::make('user.name')
                            ->label(__('custom.models.question_report.user'))
                            ->placeholder(__('custom.models.generic.empty')),
                        TextEntry::make('question.chapter_name')
                            ->label(__('custom.models.question_report.chapter'))
                            ->placeholder(__('custom.models.generic.empty')),
                        TextEntry::make('question.question')
                            ->label(__('custom.models.question_report.question'))
                            ->placeholder(__('custom.models.generic.empty'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
