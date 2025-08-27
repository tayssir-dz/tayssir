<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChapterLevelResource\Pages;
use App\Models\ChapterLevel;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ChapterLevelResource extends Resource
{
    protected static ?string $model = ChapterLevel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 7;

    protected static bool $isGloballySearchable = true;

    protected static ?string $recordTitleAttribute = 'recordTitle';

    public static function getNavigationGroup(): ?string
    {
        return __('custom.nav.section.points');
    }

    public static function getModelLabel(): string
    {
        return __('custom.models.chapter_level');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.chapter_levels');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('custom.forms.chapter_level.create.section.infos'))
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->label(__('custom.models.chapter_level.name')),
                        TextInput::make('exercice_points')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->label(__('custom.models.chapter_level.exercice_points')),
                        TextInput::make('lesson_points')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->label(__('custom.models.chapter_level.lesson_points')),
                        TextInput::make('bonus')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->label(__('custom.models.chapter_level.bonus')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('custom.models.chapter_level.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('exercice_points')
                    ->badge()
                    ->label(__('custom.models.chapter_level.exercice_points'))
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lesson_points')
                    ->badge()
                    ->label(__('custom.models.chapter_level.lesson_points'))
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bonus')
                    ->badge()
                    ->label(__('custom.models.chapter_level.bonus'))
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('custom.models.chapter_level.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(true),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChapterLevels::route('/'),
            'create' => Pages\CreateChapterLevel::route('/create'),
            'edit' => Pages\EditChapterLevel::route('/{record}/edit'),
        ];
    }
}
