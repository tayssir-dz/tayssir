<?php

namespace App\Filament\Resources\MaterialResource\RelationManagers;

use App\Filament\Resources\UnitResource;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
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
                TextInput::make('name')
                    ->required()
                    ->label(__('custom.models.unit.name'))
                    ->minLength(3),
                Textarea::make("description")
                    ->rows(4)
                    ->label(__('custom.models.unit.description')),
                Select::make('subscriptions')
                    ->multiple()
                    ->relationship('subscriptions', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('custom.models.unit.subscriptions')),
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('material_unit.sort')
            ->recordTitleAttribute('name')
            ->columns([
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
