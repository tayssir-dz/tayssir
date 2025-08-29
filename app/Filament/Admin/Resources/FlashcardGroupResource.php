<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FlashcardGroupResource\Pages;
use App\Filament\Admin\Resources\FlashcardGroupResource\RelationManagers\FlashcardsRelationManager;
use App\Models\FlashcardGroup;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FlashcardGroupResource extends Resource
{
    protected static ?string $model = FlashcardGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'title';

    // Hide from navigation since we access it through Material relation
    protected static bool $shouldRegisterNavigation = false;

    public static function getModelLabel(): string
    {
        return __('custom.models.flashcard_group');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.flashcard_groups');
    }

    public static function form(Form $form): Form
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

                    TextInput::make('material.name')
                        ->label(__('custom.models.material.name'))
                        ->disabled()
                        ->dehydrated(false)
                        ->afterStateHydrated(function ($state, $record, $set) {
                            $set('material.name', $record->material->name);
                        }),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('custom.models.flashcard_group.title'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->label(__('custom.models.flashcard_group.description')),

                Tables\Columns\TextColumn::make('material.name')
                    ->label(__('custom.models.material.name'))
                    ->badge()
                    ->colors(['primary']),

                Tables\Columns\TextColumn::make('flashcards_count')
                    ->badge()
                    ->label(__('custom.models.flashcards'))
                    ->counts('flashcards')
                    ->colors(['secondary']),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            FlashcardsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFlashcardGroup::route('/'),
            'edit' => Pages\EditFlashcardGroup::route('/{record}/edit'),
        ];
    }
}
