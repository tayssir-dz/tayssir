<?php

namespace App\Filament\Resources\MaterialResource\RelationManagers;

use App\Enums\ContentDirection;
use App\Filament\Resources\UnitResource;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UnitsRelationManager extends RelationManager
{
    public static function getModelLabel(): string
    {
        return __('custom.models.unit');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.units');
    }
    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('custom.models.units');
    }
    protected static string $relationship = 'units';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('custom.forms.unit.create.section.infos'))->schema([
                    TextInput::make('name')
                        ->required()
                        ->label(__('custom.models.unit.name'))
                        ->minLength(3),

                    Select::make('direction')->native(false)
                        ->options(ContentDirection::class)
                        ->enum(ContentDirection::class)
                        ->default(ContentDirection::INHERIT)
                        ->required()
                        ->label(__('custom.direction.label')),

                    Textarea::make("description")
                        ->rows(4)
                        ->label(__('custom.models.unit.description')),

                    Select::make('subscriptions')
                        ->multiple()
                        ->relationship('subscriptions', 'name')
                        ->searchable()
                        ->preload()
                        ->label(__('custom.models.unit.subscriptions')),

                    Forms\Components\Toggle::make('active')
                        ->label(__('custom.models.active'))
                        ->default(true),
                ])->columnSpan(2),

                Section::make(__('custom.forms.unit.create.section.image'))->schema([
                    SpatieMediaLibraryFileUpload::make('image')
                        ->multiple(false)
                        ->label("")
                        ->collection('image')
                        ->image()
                        ->imageEditor(),
                ])->columnSpan(1),
            ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->paginated(true)
            ->reorderable('sort')
            ->recordTitleAttribute('name')
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->toggleable()
                    ->placeholder(__("custom.table.image.empty"))
                    ->label(__("custom.forms.unit.create.section.image"))
                    ->collection('image')
                    ->rounded(),

                TextColumn::make('name')
                    ->label(__('custom.models.unit.name')),

                TextColumn::make('description')
                    ->limit(30)
                    ->label(__('custom.models.unit.description')),

                TextColumn::make('chapters_count')
                    ->badge()
                    ->label(__('custom.models.chapters'))
                    ->counts('chapters')
                    ->colors(['primary']),

                TextColumn::make('subscriptions.name')
                    ->badge()
                    ->default("-")
                    ->label(__('custom.models.subscriptions'))
                    ->colors(['primary']),

                Tables\Columns\ToggleColumn::make('active')
                    ->label(__('custom.models.active'))
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\LinkAction::make("Details")->label(__('custom.models.unit.action.details'))->icon('heroicon-o-eye')->color('secondary')->url(fn($record) => UnitResource::getUrl("edit", ['record' => $record])),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
