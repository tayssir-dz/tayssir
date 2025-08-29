<?php

namespace App\Filament\Admin\Resources\MaterialResource\RelationManagers;

use App\Filament\Admin\Resources\FlashcardGroupResource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FlashcardGroupsRelationManager extends RelationManager
{
    protected static ?string $icon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return __('custom.models.flashcard_group');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.flashcard_groups');
    }

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('custom.models.flashcard_groups');
    }

    protected static string $relationship = 'flashcardGroups';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('custom.forms.flashcard_group.create.section.infos'))->schema([
                    TextInput::make('title')
                        ->required()
                        ->minLength(3)
                        ->label(__('custom.models.flashcard_group.title')),

                    Textarea::make('description')
                        ->rows(4)
                        ->label(__('custom.models.flashcard_group.description')),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->label(__('custom.models.flashcard_group.title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->limit(50)
                    ->label(__('custom.models.flashcard_group.description')),

                TextColumn::make('flashcards_count')
                    ->badge()
                    ->label(__('custom.models.flashcards'))
                    ->counts('flashcards')
                    ->colors(['primary']),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\LinkAction::make('Details')
                    ->label(__('custom.models.flashcard_group.action.details'))
                    ->icon('heroicon-o-eye')
                    ->color('secondary')
                    ->url(fn($record) => FlashcardGroupResource::getUrl('edit', ['record' => $record])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
