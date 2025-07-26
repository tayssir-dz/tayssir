<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoterResource\Pages;
use App\Filament\Resources\PromoterResource\RelationManagers;
use App\Models\Promoter;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Forms;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Kossa\AlgerianCities\Commune;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class PromoterResource extends Resource implements HasShieldPermissions
{
    public static function getNavigationGroup(): ?string
    {
        return Utils::isResourceNavigationGroupEnabled()
            ? __('custom.nav.section.platform')
            : '';
    }

    public static function getModelLabel(): string
    {
        return __('custom.models.promoter');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.promoters');
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
    protected static ?string $model = Promoter::class;
    protected static ?int $navigationSort = 7;
    protected static ?string $navigationIcon = 'heroicon-o-users';
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
                Section::make(__('custom.models.user.perfonal_info'))
                    ->schema([
                        TextInput::make("name")
                            ->required()
                            ->columnSpan(2)
                            ->label(__('custom.models.user.name')),

                        TextInput::make("email")
                            ->disabledOn("edit")
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->email()
                            ->label(__('custom.models.user.email')),

                        TextInput::make('phone_number')
                            ->disabledOn("edit")
                            ->label(__('custom.models.user.phone')),

                        TextInput::make("password")
                            ->password()
                            ->required()
                            ->label(__('custom.models.user.password'))
                            ->visibleOn('create'),


                        Select::make('wilaya_id')
                            ->label(__("custom.models.user.wilaya"))
                            ->relationship(name: 'wilaya', titleAttribute: __("custom.models.user.wilaya.field"))  // Select field for wilaya
                            ->searchable()
                            ->preload()
                            ->reactive()  // Makes it reactive to changes
                            ->afterStateUpdated(fn(callable $set) => $set('commune_id', null)),  // Clear commune when wilaya changes

                        Select::make('commune_id')
                            ->label(__("custom.models.user.commune"))
                            ->options(function (callable $get) {
                                $wilayaId = $get('wilaya_id');
                                $field = __("custom.models.user.wilaya.field"); // 'name' or 'arabic_name' based on the language

                                if ($wilayaId) {
                                    // Query the communes based on the selected wilaya and the dynamic field
                                    $communes = Commune::where('wilaya_id', $wilayaId)
                                        ->pluck($field, 'id')  // Return array of id => name or arabic_name
                                        ->toArray();

                                    return $communes;
                                }

                                return [];
                            })
                            ->disabled(fn(callable $get) => !$get('wilaya_id'))  // Disable if no Wilaya selected
                            ->searchable()
                            ->preload()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                if (!$get('wilaya_id')) {
                                    $set('commune_id', null);
                                }
                            })
                    ])->columnSpan(2)->columns(2),
                Group::make()->schema([
                    Section::make(__('custom.models.user.avatar'))->schema([
                        SpatieMediaLibraryFileUpload::make("avatar_url")
                            ->image()
                            ->imageEditor()
                            ->collection("avatar")
                            ->multiple(false)
                            ->label("")
                    ]),
                ])
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make("avatar")
                    ->circular()
                    ->toggleable()
                    ->label(__('custom.models.user.avatar')),
                TextColumn::make('email')
                    ->label(__('custom.models.user.email'))
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->weight(FontWeight::Bold)
                    ->size('sm'),
                TextColumn::make('name')
                    ->label(__('custom.models.user.name'))
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->weight(FontWeight::Bold)
                    ->size('sm'),
                PhoneColumn::make('phone_number')
                    ->label(__('custom.models.user.phone'))
                    ->default(__("custom.models.user.phone.empty"))
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
            //
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
