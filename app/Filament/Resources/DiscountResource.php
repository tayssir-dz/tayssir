<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscountResource\Pages;
use App\Filament\Resources\DiscountResource\RelationManagers;
use App\Models\Discount;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Tables\Columns\MoneyColumn;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;

class DiscountResource extends Resource implements HasShieldPermissions
{
    public static function getNavigationGroup(): ?string
    {
        return Utils::isResourceNavigationGroupEnabled()
            ? __('custom.nav.section.platform')
            : '';
    }

    public static function getModelLabel(): string
    {
        return __('custom.models.discount');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.discounts');
    }
    protected static ?int $navigationSort = 6;
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any'
        ];
    }
    protected static ?string $model = Discount::class;

    protected static ?string $navigationIcon = 'heroicon-o-percent-badge';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make("tabs")->columnSpan(2)->tabs([
                    Tab::make(__('custom.models.discount.tabs.informations'))
                        ->schema([
                            TextInput::make("name")
                                ->unique()
                                ->label(__("custom.models.discount.name"))
                                ->required()
                                ->columnSpan(2),

                            Textarea::make("description")
                                ->rows(4)
                                ->label(__("custom.models.discount.description"))
                                ->columnSpan(2),
                        ]),
                    Tab::make(__('custom.models.discount.tabs.reduction'))
                        ->schema([
                            MoneyInput::make("amount")
                                ->label(__("custom.models.discount.amount"))
                                ->default(0)
                                ->required()
                                ->locale(__("custom.currency.local.dzd"))
                                ->columnSpan(2),

                            TextInput::make("percentage")
                                ->label(__("custom.models.discount.percentage"))
                                ->numeric()
                                ->default(0)
                                ->required()
                                ->columnSpan(2),
                        ]),
                    Tab::make(__('custom.models.discount.tabs.period'))
                        ->schema([
                            DatePicker::make("from")
                                ->label(__("custom.models.discount.from"))
                                ->required(),

                            DatePicker::make("to")
                                ->label(__("custom.models.discount.to"))
                                ->required()
                        ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(true)
            ->columns([
                TextColumn::make("name")
                    ->label(__("custom.models.discount.name"))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('subscriptions.name')
                    ->default(__("custom.models.discount.subscriptions.empty"))
                    ->badge()->color("primary")
                    ->sortable()
                    ->searchable()
                    ->label(__("custom.models.discount.subscriptions")),

                MoneyColumn::make('amount')
                    ->badge()->color("gray")
                    ->locale(__("custom.currency.local.dzd"))
                    ->label(__("custom.models.discount.amount")),

                TextColumn::make("percentage")
                    ->badge()
                    ->color("gray")
                    ->label(__("custom.models.discount.percentage")),
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
            'index' => Pages\ListDiscounts::route('/'),
            'create' => Pages\CreateDiscount::route('/create'),
            'edit' => Pages\EditDiscount::route('/{record}/edit'),
        ];
    }
}
