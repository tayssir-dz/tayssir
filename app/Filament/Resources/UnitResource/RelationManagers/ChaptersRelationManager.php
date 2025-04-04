<?php

namespace App\Filament\Resources\UnitResource\RelationManagers;

use App\Enums\ContentDirection;
use App\Filament\Resources\ChapterResource;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChaptersRelationManager extends RelationManager
{
    public static function getModelLabel(): string
    {
        return __('custom.models.chapter');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.chapters');
    }
    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('custom.models.chapters');
    }
    protected static string $relationship = 'chapters';
    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('custom.forms.chapter.create.section.infos'))->schema([
                    TextInput::make('name')
                        ->required()
                        ->minLength(3)
                        ->label(__('custom.models.chapter.name')),


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
                        // ->searchable()
                        ->preload()
                        ->label(__('custom.models.chapter.subscriptions')),

                    Textarea::make("description")
                        ->rows(4)
                        ->label(__('custom.models.chapter.description')),

                    Forms\Components\Toggle::make('active')
                        ->label(__('custom.models.active'))
                        ->default(true),

                ])->columnSpan(2),
                Section::make(__('custom.forms.chapter.create.section.image'))->schema([
                    SpatieMediaLibraryFileUpload::make('photo')
                        ->label("")
                        ->multiple(false)
                        ->collection('chapter_photos')
                        ->image()
                        ->imageEditor(),
                ])->columnSpan(1),
            ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->reorderable('sort')
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make("id"),
                SpatieMediaLibraryImageColumn::make('photo')
                    ->placeholder(__("custom.table.image.empty"))
                    ->collection('chapter_photos')
                    ->label(__('custom.models.chapter.photo')),

                TextColumn::make('name')
                    ->label(__('custom.models.chapter.name'))
                    ->sortable(),
                // ->searchable(),

                TextColumn::make('description')
                    ->limit(30)
                    ->label(__('custom.models.chapter.description')),

                TextColumn::make('questions_count')
                    ->badge()
                    ->label(__('custom.models.questions'))
                    ->counts('questions')
                    ->colors(['primary']),

                TextColumn::make("subscriptions.name")
                    ->label(__("custom.models.subscriptions"))
                    ->badge(),

                Tables\Columns\ToggleColumn::make('active')
                    ->label(__('custom.models.active'))
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                // Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\LinkAction::make("Details")->icon('heroicon-o-eye')->color('secondary')->url(fn($record) => ChapterResource::getUrl("edit", ['record' => $record]))
                    ->label(__('custom.models.chapter.action.details')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
