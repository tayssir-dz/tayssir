<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\AdminNavigation;
use App\Filament\Admin\Resources\QuestionReportResource\Pages;
use App\Filament\Admin\Resources\QuestionReportResource\RelationManagers;
use App\Models\QuestionReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\IconEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
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
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn($record) => static::getUrl('view', ['record' => $record]))
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['user', 'question']))
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('custom.models.question_report.user'))
                    ->placeholder(__('custom.models.generic.empty'))
                    ->toggleable(),
                TextColumn::make('description')
                    ->grow()
                    ->label(__('custom.models.question_report.description'))
                    ->limit(25)
                    ->placeholder(__('custom.models.generic.empty'))
                    ->toggleable(),
                TextColumn::make('question.chapter_name')
                    ->limit(25)
                    ->grow()
                    ->label(__('custom.models.question_report.chapter'))
                    ->placeholder(__('custom.models.generic.empty'))
                    ->toggleable(),
                TextColumn::make('question.question')
                    ->label(__('custom.models.question_report.question'))
                    ->limit(25)
                    ->grow()
                    ->toggleable()
                    ->placeholder(__('custom.models.generic.empty')),

                IconColumn::make('is_solved')
                    ->boolean()
                    ->alignCenter()
                    ->label(__('custom.models.question_report.is_solved'))
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('is_contacted')
                    ->boolean()
                    ->alignCenter()
                    ->label(__('custom.models.question_report.is_contacted'))
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([

                // TernaryFilter::make('is_solved')
                //     ->label(__("custom.models.question_report.is_solved")),
                // TernaryFilter::make('is_contacted')
                //     ->label(__("custom.models.question_report.is_contacted"))
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('markAsSolved')
                        ->icon(fn($record): string => $record->is_solved ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->label(fn($record): string => $record->is_solved ? __('custom.models.question_report.actions.mark_as_unsolved') : __('custom.models.question_report.actions.mark_as_solved'))
                        ->color(fn($record): string => $record->is_solved ? 'danger' : 'success')
                        ->action(function ($record) {
                            $record->is_solved = ! $record->is_solved;
                            $record->save();
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\Action::make('markAsContacted')
                        ->icon(fn($record): string => $record->is_contacted ? 'heroicon-o-phone-x-mark' : 'heroicon-o-phone')
                        ->label(fn($record): string => $record->is_contacted ? __('custom.models.question_report.actions.mark_as_not_contacted') : __('custom.models.question_report.actions.mark_as_contacted'))
                        ->color(fn($record): string => $record->is_contacted ? 'danger' : 'info')
                        ->action(function ($record) {
                            $record->is_contacted = ! $record->is_contacted;
                            $record->save();
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\Action::make("goToChapter")
                        ->label(__("custom.models.question_report.question"))
                        ->icon('heroicon-o-question-mark-circle')
                        ->url(fn($record) => ChapterResource::getUrl('edit', ['record' => $record->question->chapter()->first()]))
                        ->color("info"),
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
                        TextEntry::make('description')
                            ->label(__('custom.models.question_report.description'))
                            ->placeholder(__('custom.models.generic.empty'))
                            ->columnSpanFull(),
                        IconEntry::make('is_solved')
                            ->label(__('custom.models.question_report.is_solved'))
                            ->boolean(),
                        IconEntry::make('is_contacted')
                            ->label(__('custom.models.question_report.is_contacted'))
                            ->boolean(),
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
