<?php

namespace App\Filament\Resources;

use App\Enums\ContentDirection;
use App\Filament\Resources\UnitResource\Pages;
use App\Filament\Resources\UnitResource\RelationManagers;
use App\Filament\Resources\UnitResource\RelationManagers\ChaptersRelationManager;
use App\Models\Unit;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\IconColumn;

class UnitResource extends Resource implements HasShieldPermissions
{
    public static function getNavigationGroup(): ?string
    {
        return Utils::isResourceNavigationGroupEnabled()
            ? __('custom.nav.section.content')
            : '';
    }

    public static function getModelLabel(): string
    {
        return __('custom.models.unit');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.units');
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
    protected static ?string $model = Unit::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?int $navigationSort = 9;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('name')->required()->minLength(3)->label(__('custom.models.unit.name')),
                    Select::make('material_units')->relationship('material_units', 'code')->searchable()->preload()->required()->label(__('custom.models.unit.material')),

                    Select::make('direction')
                        ->options(ContentDirection::class)
                        ->enum(ContentDirection::class)
                        ->required()
                        ->label(__('custom.direction.label')),

                    Textarea::make("description")->rows(4)->columnSpan(2)->label(__('custom.models.unit.description')),
                    Select::make('subscriptions')
                        ->multiple()
                        ->relationship('subscriptions', 'name')
                        ->searchable()
                        ->preload()
                        ->label(__('custom.models.unit.subscriptions'))
                        ->columnSpan(2),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('custom.models.unit.name'))->sortable()->searchable(),
                TextColumn::make("subscriptions.name")
                    ->badge(),
                TextColumn::make('description')->limit(30)->label(__('custom.models.unit.description')),
                TextColumn::make('material.code')->badge()->label(__('custom.models.unit.material'))->sortable()->searchable()->sortable(),
                TextColumn::make('chapters_count')
                    ->badge()
                    ->label(__('custom.models.chapters'))
                    ->counts('chapters')
                    ->sortable()
                    ->colors(['primary']),
                TextColumn::make('subscriptions_count')
                    ->badge()
                    ->label(__('custom.models.subscriptions'))
                    ->counts('subscriptions')
                    ->sortable()
                    ->colors(['primary']),
            ])
            ->filters([
                // Tables\Filters\SelectFilter::make('material')->relationship("material", "code")->multiple()->preload()
                //     ->searchable()->label(__('custom.models.materials')),
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
            ChaptersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'edit' => Pages\EditUnit::route('/{record}/edit'),
        ];
    }
}
