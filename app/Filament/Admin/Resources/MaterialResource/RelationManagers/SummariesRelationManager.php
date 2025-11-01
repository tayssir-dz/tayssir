<?php

namespace App\Filament\Admin\Resources\MaterialResource\RelationManagers;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SummariesRelationManager extends RelationManager
{
    protected static ?string $icon = 'heroicon-o-folder-arrow-down';

    public static function getModelLabel(): string
    {
        return __('custom.models.summary');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.summaries');
    }

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('custom.models.summaries');
    }

    protected static string $relationship = 'summaries';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('custom.forms.summary.create.section.infos'))->schema([
                    TextInput::make('title')
                        ->required()
                        ->minLength(3)
                        ->label(__('custom.models.summary.title')),

                    Textarea::make('description')
                        ->rows(4)
                        ->label(__('custom.models.summary.description')),

                    Toggle::make('is_active')
                        ->label(__('custom.models.summary.is_active'))
                        ->default(true),
                ])->columnSpan(2),

                Section::make(__('custom.forms.summary.create.section.file'))->schema([
                    SpatieMediaLibraryFileUpload::make('pdf')
                        ->multiple(false)
                        ->label('')
                        ->collection('pdf')
                        ->acceptedFileTypes(['application/pdf'])
                        ->downloadable()
                        ->openable()
                        ->previewable(false),
                ])->columnSpan(1),
            ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                IconColumn::make('pdf')
                    ->label(__('custom.models.summary.pdf'))
                    ->icon('heroicon-o-document-text')
                    ->color(fn($record) => $record->pdf ? 'success' : 'gray')
                    ->tooltip(fn($record) => $record->pdf ? 'PDF Available' : 'No PDF'),

                TextColumn::make('title')
                    ->label(__('custom.models.summary.title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->limit(50)
                    ->label(__('custom.models.summary.description')),

                ToggleColumn::make('is_active')
                    ->label(__('custom.models.summary.is_active'))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('custom.models.summary.is_active'))
                    ->boolean()
                    ->trueLabel(__('custom.models.active.true'))
                    ->falseLabel(__('custom.models.active.false'))
                    ->native(false),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                SummariesUploadManyAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => $record->pdf)
                    ->openUrlInNewTab()
                    ->visible(fn($record) => $record->pdf)
                    ->label('Download PDF'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
