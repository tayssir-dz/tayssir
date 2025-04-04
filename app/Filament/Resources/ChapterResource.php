<?php

namespace App\Filament\Resources;

use App\Enums\ContentDirection;
use App\Filament\Resources\ChapterResource\Pages;
use App\Filament\Resources\ChapterResource\RelationManagers;
use App\Filament\Resources\ChapterResource\RelationManagers\QuestionsRelationManager;
use App\Models\Chapter;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChapterResource extends Resource implements HasShieldPermissions
{
    public static function getNavigationGroup(): ?string
    {
        return Utils::isResourceNavigationGroupEnabled()
            ? __('custom.nav.section.content')
            : '';
    }

    public static function getModelLabel(): string
    {
        return __('custom.models.chapter');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.chapters');
    }
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }
    protected static ?string $model = Chapter::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 12;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('custom.forms.chapter.create.section.infos'))->schema([
                    TextInput::make('name')
                        ->required()
                        ->minLength(3)
                        ->label(__('custom.models.chapter.name')),

                    Select::make('unit')
                        ->relationship('unit', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->label(__('custom.models.chapter.unit')),

                    Select::make('chapter_level_id')
                        ->relationship('chapter_level', 'name')
                        ->createOptionForm([
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
                        ])
                        ->searchable()
                        ->preload()
                        ->required()
                        ->label(__('custom.models.chapter.level')),

                    Select::make('direction')->native(false)
                        ->options(ContentDirection::class)
                        ->enum(ContentDirection::class)
                        ->default(ContentDirection::INHERIT)
                        ->required()
                        ->label(__('custom.direction.label')),

                    Select::make('subscriptions')
                        ->multiple()
                        ->relationship('subscriptions', 'name')
                        ->searchable()
                        ->preload()
                        ->label(__('custom.models.chapter.subscriptions'))
                        ->columnSpan(2),

                    Textarea::make("description")
                        ->rows(4)
                        ->columnSpan(2)
                        ->label(__('custom.models.chapter.description')),

                    Forms\Components\Toggle::make('active')
                        ->label(__('custom.models.active'))
                        ->default(true),
                ])->columnSpan(2)
                    ->columns(2),

                Section::make(__('custom.forms.chapter.create.section.image'))->schema([
                    SpatieMediaLibraryFileUpload::make('photo')
                        ->multiple(false)
                        ->collection('chapter_photos')
                        ->image()
                        ->imageEditor()
                        ->label(''),
                ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('photo')
                    ->collection('chapter_photos')
                    ->placeholder(__("custom.table.image.empty"))
                    ->rounded()
                    ->label(__('custom.models.chapter.photo')),

                TextColumn::make('name')
                    ->label(__('custom.models.chapter.name'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('description')
                    ->limit(30)
                    ->label(__('custom.models.chapter.description')),

                TextColumn::make('unit.name')
                    ->badge()
                    ->colors(['gray'])
                    ->label(__('custom.models.chapter.unit')),

                TextColumn::make('questions_count')
                    ->badge()
                    ->label(__('custom.models.questions'))
                    ->counts('questions')
                    ->sortable()
                    ->colors(['primary']),

                TextColumn::make("subscriptions.name")
                    ->label(__("custom.models.subscriptions"))
                    ->badge(),

                Tables\Columns\ToggleColumn::make('active')
                    ->label(__('custom.models.active'))
                    ->sortable()
                    ->toggleable(),
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
            QuestionsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChapters::route('/'),
            'create' => Pages\CreateChapter::route('/create'),
            'edit' => Pages\EditChapter::route('/{record}/edit'),
        ];
    }
}
