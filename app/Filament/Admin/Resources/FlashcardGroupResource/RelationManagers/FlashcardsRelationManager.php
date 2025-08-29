<?php

namespace App\Filament\Admin\Resources\FlashcardGroupResource\RelationManagers;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FlashcardsRelationManager extends RelationManager
{
    protected static ?string $icon = 'heroicon-o-credit-card';

    public static function getModelLabel(): string
    {
        return __('custom.models.flashcard');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.flashcards');
    }

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('custom.models.flashcards');
    }

    protected static string $relationship = 'flashcards';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('custom.forms.flashcard.create.section.infos'))->schema([
                    TextInput::make('title')
                        ->required()
                        ->minLength(3)
                        ->label(__('custom.models.flashcard.title')),

                    Textarea::make('description')
                        ->rows(4)
                        ->label(__('custom.models.flashcard.description')),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->label(__('custom.models.flashcard.title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap()
                    ->searchable()
                    ->label(__('custom.models.flashcard.description')),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                FlashcardJsonUploadAction::make(),
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
