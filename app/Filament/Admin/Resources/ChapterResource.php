<?php

namespace App\Filament\Admin\Resources;

use App\Enums\ContentDirection;
use App\Filament\Admin\AdminNavigation;
use App\Filament\Admin\Resources\ChapterResource\Pages;
use App\Filament\Admin\Resources\ChapterResource\RelationManagers\QuestionsRelationManager;
use App\Models\Chapter;
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

class ChapterResource extends Resource
{
    public static function getNavigationGroup(): ?string
    {
        return __(AdminNavigation::CHAPTER_RESOURCE['group']);
    }

    public static function getModelLabel(): string
    {
        return __('custom.models.chapter');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.chapters');
    }

    protected static ?string $model = Chapter::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = true;

    protected static ?string $navigationIcon = AdminNavigation::CHAPTER_RESOURCE['icon'];

    protected static ?int $navigationSort = AdminNavigation::CHAPTER_RESOURCE['sort'];

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
                        //->preload()
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

                    Textarea::make('description')
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
                    ->conversion('thumb')
                    ->placeholder(__('custom.table.image.empty'))
                    ->circular()
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

                TextColumn::make('subscriptions.name')
                    ->label(__('custom.models.subscriptions'))
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
            QuestionsRelationManager::class,
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
