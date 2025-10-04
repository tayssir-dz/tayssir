<?php

namespace App\Filament\Admin\Resources;

use App\Enums\ContentDirection;
use App\Filament\Admin\AdminNavigation;
use App\Filament\Admin\Resources\MaterialResource\Pages;
use App\Filament\Admin\Resources\MaterialResource\RelationManagers\BacsRelationManager;
use App\Filament\Admin\Resources\MaterialResource\RelationManagers\FlashcardGroupsRelationManager;
use App\Filament\Admin\Resources\MaterialResource\RelationManagers\SummariesRelationManager;
use App\Filament\Admin\Resources\MaterialResource\RelationManagers\UnitsRelationManager;
use App\Models\Material;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class MaterialResource extends Resource
{
    protected static ?string $recordTitleAttribute = 'code';

    public static function getNavigationGroup(): ?string
    {
        return __(AdminNavigation::MATERIAL_RESOURCE['group']);
    }

    public static function getModelLabel(): string
    {
        return __('custom.models.material');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.materials');
    }

    protected static ?string $model = Material::class;

    protected static ?string $navigationIcon = AdminNavigation::MATERIAL_RESOURCE['icon'];

    protected static ?int $navigationSort = AdminNavigation::MATERIAL_RESOURCE['sort'];

    protected static bool $isGloballySearchable = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('custom.forms.material.create.section.infos'))->schema([
                    TextInput::make('name')->required()->minLength(3)
                        ->label(__('custom.models.material.name')),

                    TextInput::make('code')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->minLength(3)
                        ->placeholder(__('custom.models.material.code'))
                        ->label(__('custom.models.material.code'))
                        ->columnSpan(1),

                    ColorPicker::make('color')
                        ->required()
                        ->label(__('custom.models.material.color')),

                    ColorPicker::make('secondary_color')
                        ->label(__('custom.models.material.secondary_color')),

                    Select::make('divisions.name')
                        ->relationship('divisions', 'name')
                        ->multiple()
                        ->required()
                        // ->preload()
                        ->label(__('custom.models.material.division')),

                    // Forms\Components\Toggle::make('rtl')
                    //     ->label('rtl'),
                    Select::make('direction')->native(false)
                        ->options([
                            ContentDirection::RTL->value => ContentDirection::RTL->getLabel(),
                            ContentDirection::LTR->value => ContentDirection::LTR->getLabel(),
                        ])
                        ->default(ContentDirection::RTL->value)
                        ->required()
                        ->label(__('custom.direction.label')),

                    Textarea::make('description')
                        ->rows(4)
                        ->columnSpan(2)
                        ->label(__('custom.models.material.description')),

                    Forms\Components\Toggle::make('active')
                        ->label(__('custom.models.active'))
                        ->default(false),

                ])->columnSpan(2)
                    ->columns(2),

                Section::make(__('custom.forms.material.create.section.image'))->schema([
                    SpatieMediaLibraryFileUpload::make('image')
                        ->multiple(false)
                        ->label(__('custom.models.material.image'))
                        ->collection('image')
                        ->image()
                        ->imageEditor(),

                    SpatieMediaLibraryFileUpload::make('image_grid')
                        ->multiple(false)
                        ->label(__('custom.models.material.image_grid'))
                        ->collection('image_grid')
                        ->image()
                        ->imageEditor(),

                ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->toggleable()
                    ->circular()
                    ->placeholder(__('custom.table.image.empty'))
                    ->label(__('custom.models.material.image'))
                    ->collection('image')
                    ->conversion('thumb'),

                SpatieMediaLibraryImageColumn::make('image_grid')
                    ->toggleable()
                    ->circular()
                    ->placeholder(__('custom.table.image.empty'))
                    ->label(__('custom.models.material.image_grid'))
                    ->collection('image_grid')
                    ->conversion('thumb'),

                TextColumn::make('name')
                    ->label(__('custom.models.material.name'))
                    ->searchable()
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->badge()
                    ->alignCenter()
                    ->colors(['primary'])
                    ->label(__('custom.models.material.code')),

                ColorColumn::make('color')
                    ->toggleable()
                    ->label(__('custom.models.material.color')),

                ColorColumn::make('secondary_color')
                    ->toggleable()
                    ->label(__('custom.models.material.secondary_color'))
                    ->default('#000000'),

                TextColumn::make('description')
                    ->toggleable()
                    ->label(__('custom.models.material.description'))
                    ->limit(30),

                TextColumn::make('divisions.name')
                    ->toggleable()
                    ->label(__('custom.models.material.division'))
                    ->badge()
                    ->colors(['gray'])
                    ->searchable()
                    ->sortable(),

                TextColumn::make('units_count')
                    ->badge()
                    ->label(__('custom.models.units'))
                    ->counts('units')
                    ->sortable()
                    ->toggleable()
                    ->colors(['primary'])
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('active')
                    ->label(__('custom.models.active'))
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('divisions.name')
                    ->relationship('divisions', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->label(__('custom.models.divisions')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('clone')
                    ->label(__('custom.models.material.actions.clone'))
                    ->icon('heroicon-o-document-duplicate')
                    ->requiresConfirmation()
                    ->action(function (Material $record) {
                        $cloned = $record->deepClone();
                        Notification::make()
                            ->title(__('custom.models.material.actions.clone'))
                            ->body(__('custom.models.material') . ': ' . $cloned->name)
                            ->success()
                            ->send();
                    }),
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
            UnitsRelationManager::class,
            SummariesRelationManager::class,
            BacsRelationManager::class,
            FlashcardGroupsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaterials::route('/'),
            'create' => Pages\CreateMaterial::route('/create'),
            'edit' => Pages\EditMaterial::route('/{record}/edit'),
        ];
    }
}
