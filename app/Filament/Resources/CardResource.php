<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CardResource\CardFormUtils;
use App\Filament\Resources\CardResource\Pages;
use App\Filament\Resources\CardResource\RelationManagers;
use App\Models\Card;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Tables\Columns\MoneyColumn;

class CardResource extends Resource implements HasShieldPermissions
{
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getNavigationGroup(): ?string
    {
        return null;
        // return Utils::isResourceNavigationGroupEnabled()
        //     ? __('custom.nav.section.platform')
        //     : '';
    }

    public static function getModelLabel(): string
    {
        return __('custom.models.card');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.cards');
    }
    public static function getNavigationBadge(): ?string
    {
        return Utils::isResourceNavigationBadgeEnabled()
            ? strval(static::getEloquentQuery()->count())
            : null;
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
            'create_many'
        ];
    }
    protected static ?string $model = Card::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 5;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make(__("custom.models.card.tab.pricing"))
                            ->schema([
                                MoneyInput::make('price')
                                    ->locale(__("custom.currency.local.dzd"))
                                    ->label(__("custom.models.card.price"))
                                    ->default(0)
                                    ->required()
                                    ->afterStateUpdated(fn($get, $set) => CardFormUtils::priceStateHandeler($get, $set))
                                    ->live(onBlur: true),

                                Toggle::make("is_on_discount")
                                    ->default(false)
                                    ->label(__("custom.models.card.is_on_discount"))
                                    ->afterStateUpdated(fn($get, $set) => CardFormUtils::priceStateHandeler($get, $set))
                                    ->live(onBlur: true),

                                MoneyInput::make('discount_price')
                                    ->locale(__("custom.currency.local.dzd"))
                                    ->label(__("custom.models.card.discount_price"))
                                    ->visible(fn($get) => $get('is_on_discount'))
                                    ->default(0)
                                    ->minValue(0)
                                    ->required()
                                    ->afterStateUpdated(fn($get, $set) => CardFormUtils::priceStateHandeler($get, $set))
                                    ->live(onBlur: true),

                                TextInput::make("discount_percentage")
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->minValue(0)->maxValue(100)
                                    ->label(__("custom.models.card.discount_percentage"))
                                    ->visible(fn($get) => $get('is_on_discount'))
                                    ->afterStateUpdated(fn($get, $set) => CardFormUtils::priceStateHandeler($get, $set))
                                    ->live(onBlur: true),

                                // Display Price is calculated based on the discount price and the discount percentage, disabled for the user to edit
                                MoneyInput::make('display_price')
                                    ->locale(__("custom.currency.local.dzd"))
                                    ->label(__("custom.models.card.display_price"))
                                    ->disabled()
                                    ->default(0)
                                    ->required(),
                            ]),
                        Tab::make(__("custom.models.card.tab.subscription"))
                            ->schema([
                                Select::make("subscription_type")
                                    ->required()
                                    ->options([
                                        "yearly_subscription" => __("custom.models.card.subscription.yearly"),
                                    ])
                                    ->label(__("custom.models.card.subscription_type")),
                                // expires at

                                DatePicker::make("expires_at")
                                    ->label(__("custom.models.card.expires_at"))
                                    ->required(),
                                DatePicker::make("activated_at")
                                    ->label(__("custom.models.card.activated_at"))
                                    ->required()
                            ]),
                        Tab::make(__("custom.models.card.tab.code"))
                            ->schema([
                                TextInput::make("code")
                                    ->label(__("custom.models.card.code"))
                                    ->required()
                                    ->placeholder(__("custom.models.card.code.warning")),

                            ]),
                    ])


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("code")
                    ->label(__("custom.models.card.code"))
                    ->searchable()->sortable(),
                MoneyColumn::make('price')
                    ->badge()->color("success")
                    ->locale(__("custom.currency.local.dzd"))
                    ->label(__("custom.models.card.price"))
                    ->color('gray'),
                ToggleColumn::make("is_on_discount")
                    ->label(__("custom.models.card.is_on_discount")),
                MoneyColumn::make('display_price')
                    ->badge()->color("success")
                    ->locale(__("custom.currency.local.dzd"))
                    ->label(__("custom.models.card.display_price")),
                TextColumn::make("expires_at")->label(__("custom.models.card.expires_at"))->since(),
                // TextColumn::make("activated_at")->label(__("custom.models.card.activated_at"))->since(),
                // column for status, badge with difference colors for those values 'idle', 'expired', 'active', 'done', 'problem (with translation)
                BadgeColumn::make('status')
                    ->label(__("custom.models.card.status"))
                    ->formatStateUsing(fn($state) => __("custom.models.card.status.$state"))
                    ->color(fn($record) => match ($record->status) {
                        'idle' => 'info',
                        'expired' => 'danger',
                        'active' => 'success',
                        'done' => 'gray',
                        'problem' => 'danger',
                    })
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->headerActions([
                // Tables\Actions\ExportAction::make(),
                // Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListCards::route('/'),
            'create' => Pages\CreateCard::route('/create'),
            'edit' => Pages\EditCard::route('/{record}/edit'),
        ];
    }
}
