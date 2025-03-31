<?php

namespace App\Filament\Resources\DivisionResource\RelationManagers;

use App\Enums\ContentDirection;
use App\Filament\Resources\MaterialResource;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaterialsRelationManager extends RelationManager
{
    public static function getModelLabel(): string
    {
        return __('custom.models.material');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.materials');
    }

    protected static string $relationship = 'materials';
    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('custom.models.materials');
    }



    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('custom.forms.material.create.section.infos'))->schema([
                    TextInput::make("name")
                        ->required()
                        ->minLength(3)
                        ->label(__('custom.models.material.name')),
                    TextInput::make("code")
                        ->required()
                        ->minLength(3)
                        ->placeholder(__('custom.models.material.code'))
                        ->label(__('custom.models.material.code')),

                    ColorPicker::make("color")
                        ->required()
                        ->label(__('custom.models.material.color')),

                    ColorPicker::make("secondary_color")
                        ->label(__('custom.models.material.secondary_color')),

                    Select::make('direction')->native(false)
                        ->options([
                            ContentDirection::RTL->value => ContentDirection::RTL->getLabel(),
                            ContentDirection::LTR->value => ContentDirection::LTR->getLabel(),
                        ])
                        ->default(ContentDirection::RTL)
                        ->required()
                        ->label(__('custom.direction.label')),

                    Textarea::make("description")
                        ->rows(4)
                        ->label(__('custom.models.material.description')),

                ])->columnSpan(2),
                Section::make(__('custom.forms.material.create.section.image'))->schema([
                    SpatieMediaLibraryFileUpload::make('image')
                        ->label("")
                        ->multiple(false)
                        ->collection('image')
                        ->image()
                        ->imageEditor(),

                    SpatieMediaLibraryFileUpload::make('image_grid')
                        ->label(__('custom.models.material.image_grid'))
                        ->multiple(false)
                        ->collection('image_grid')
                        ->image()
                        ->imageEditor(),
                ])->columnSpan(1),
            ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->recordTitleAttribute('name')
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->label(__("custom.forms.material.create.section.image"))
                    ->placeholder(__("custom.table.image.empty"))
                    ->collection('image')
                    ->rounded(),
                TextColumn::make("name")->label(__("custom.models.material.name")),
                TextColumn::make("code")
                    ->badge()
                    ->colors(['primary'])
                    ->label(__("custom.models.material.code")),
                TextColumn::make("description")->label(__("custom.models.material.description"))->limit(30),
                ColorColumn::make("color")
                    ->label(__("custom.models.material.color")),
                ColorColumn::make("secondary_color")
                    ->label(__("custom.models.material.secondary_color")),
                TextColumn::make('division.name')->label(__("custom.models.material.division"))->badge()->colors(['gray']),
                TextColumn::make('units_count')
                    ->badge()
                    ->label(__('custom.models.units'))
                    ->counts('units')
                    ->colors(['primary']),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\LinkAction::make('Details')
                        ->label(__('custom.models.material.action.details'))
                        ->icon('heroicon-o-eye')
                        ->color('secondary')
                        ->url(fn($record) => MaterialResource::getUrl("edit", ['record' => $record])),
                    Tables\Actions\DetachAction::make()
                        ->icon('heroicon-o-x-mark')
                ])
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\DetachBulkAction::make()
                        ->icon('heroicon-o-x-mark')
                ]),
            ]);
    }
}
