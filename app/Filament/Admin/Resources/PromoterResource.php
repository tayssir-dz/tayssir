<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\AdminNavigation;
use App\Filament\Admin\Resources\PromoterResource\Pages;
use App\Filament\Admin\Resources\PromoterResource\RelationManagers\PromoCodesRelationManager;
use App\Models\Promoter;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Kossa\AlgerianCities\Commune;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class PromoterResource extends Resource
{
    public static function getNavigationGroup(): ?string
    {
        return __(AdminNavigation::PROMOTER_RESOURCE['group']);
    }

    public static function getModelLabel(): string
    {
        return __('custom.models.promoter');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.promoters');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static ?string $model = Promoter::class;

    protected static ?int $navigationSort = AdminNavigation::PROMOTER_RESOURCE['sort'];

    protected static ?string $navigationIcon = AdminNavigation::PROMOTER_RESOURCE['icon'];

    protected static bool $isGloballySearchable = true;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'phone_number', 'email', 'new_email', 'wilaya.arabic_name', 'commune.arabic_name', 'wilaya.name', 'commune.name'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('custom.models.promoter.personal_info'))
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->columnSpan(2)
                            ->label(__('custom.models.promoter.name')),

                        TextInput::make('email')
                            ->disabledOn('edit')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->email()
                            ->label(__('custom.models.promoter.email')),

                        TextInput::make('phone_number')
                            ->disabledOn('edit')
                            ->label(__('custom.models.promoter.phone')),

                        TextInput::make('password')
                            ->password()
                            ->required()
                            ->label(__('custom.models.promoter.password'))
                            ->visibleOn('create'),

                        Select::make('wilaya_id')
                            ->label(__('custom.models.promoter.wilaya'))
                            ->relationship(name: 'wilaya', titleAttribute: __('custom.models.promoter.wilaya.field'))  // Select field for wilaya
                            ->searchable()
                            ->preload()
                            ->reactive()  // Makes it reactive to changes
                            ->afterStateUpdated(fn(callable $set) => $set('commune_id', null)),  // Clear commune when wilaya changes

                        Select::make('commune_id')
                            ->label(__('custom.models.promoter.commune'))
                            ->options(function (callable $get) {
                                $wilayaId = $get('wilaya_id');
                                $field = __('custom.models.promoter.wilaya.field'); // 'name' or 'arabic_name' based on the language

                                if ($wilayaId) {
                                    // Query the communes based on the selected wilaya and the dynamic field
                                    $communes = Commune::where('wilaya_id', $wilayaId)
                                        ->pluck($field, 'id')  // Return array of id => name or arabic_name
                                        ->toArray();

                                    return $communes;
                                }

                                return [];
                            })
                            ->disabled(fn(callable $get) => ! $get('wilaya_id'))  // Disable if no Wilaya selected
                            ->searchable()
                            ->preload()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                if (! $get('wilaya_id')) {
                                    $set('commune_id', null);
                                }
                            }),
                    ])->columnSpan(2)->columns(2),
                Group::make()->schema([
                    Section::make(__('custom.models.promoter.avatar'))->schema([
                        SpatieMediaLibraryFileUpload::make('avatar_url')
                            ->image()
                            ->imageEditor()
                            ->collection('avatar')
                            ->multiple(false)
                            ->label(''),
                    ]),
                ]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular()
                    ->toggleable()
                    ->label(__('custom.models.promoter.avatar')),
                TextColumn::make('email')
                    ->label(__('custom.models.promoter.email'))
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->weight(FontWeight::Bold)
                    ->size('sm'),
                TextColumn::make('name')
                    ->label(__('custom.models.promoter.name'))
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->weight(FontWeight::Bold)
                    ->size('sm'),
                PhoneColumn::make('phone_number')
                    ->label(__('custom.models.promoter.phone'))
                    ->default(__('custom.models.promoter.phone.empty'))
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->size('sm')
                    ->copyMessage('Phone number copied')
                    ->copyMessageDuration(1500),
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
            PromoCodesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromoters::route('/'),
            'create' => Pages\CreatePromoter::route('/create'),
            'edit' => Pages\EditPromoter::route('/{record}/edit'),
        ];
    }
}
