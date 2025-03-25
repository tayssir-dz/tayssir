<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DivisionResource\Pages;
use App\Filament\Resources\DivisionResource\RelationManagers;
use App\Filament\Resources\DivisionResource\RelationManagers\MaterialsRelationManager;
use App\Models\Division;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DivisionResource extends Resource implements HasShieldPermissions
{
    protected static ?string $recordTitleAttribute = 'name';
    public static function getNavigationGroup(): ?string
    {
        return Utils::isResourceNavigationGroupEnabled()
            ? __('custom.nav.section.content')
            : '';
    }

    public static function getModelLabel(): string
    {
        return __('custom.models.division');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.divisions');
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
    protected static ?string $model = Division::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('custom.forms.division.create.section.infos'))->schema([
                    TextInput::make("name")->required()->minLength(3)->label(__('custom.models.division.name'))->unique(),
                    Textarea::make("description")->rows(4)->columnSpan(2)->label(__('custom.models.division.description')),
                ])->columnSpan(2),
                Section::make(__('custom.forms.division.create.section.image'))->schema([
                    SpatieMediaLibraryFileUpload::make('image')
                        ->multiple(false)
                        ->label("")
                        ->collection('image')
                        ->imageEditor()
                        ->image()
                ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')->circular()
                    ->label(__('custom.forms.division.create.section.image'))
                    ->placeholder(__("custom.table.image.empty"))
                    ->collection('image'),
                TextColumn::make("name")
                    ->label(__('custom.models.division.name'))
                    ->searchable()->sortable(),
                TextColumn::make("description")->limit(30)
                    ->label(__('custom.models.division.description')),
                TextColumn::make('materials_count')
                    ->badge()
                    ->label(__('custom.models.materials'))
                    ->counts('materials')
                    ->sortable()
                    ->colors(['primary'])
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
            MaterialsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDivisions::route('/'),
            'create' => Pages\CreateDivision::route('/create'),
            'edit' => Pages\EditDivision::route('/{record}/edit'),
        ];
    }
}
