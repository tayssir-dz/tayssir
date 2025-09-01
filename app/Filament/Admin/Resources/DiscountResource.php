<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\AdminNavigation;
use App\Filament\Admin\Resources\DiscountResource\Pages;
use App\Models\Discount;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Tables\Columns\MoneyColumn;

class DiscountResource extends Resource
{
    public static function getNavigationGroup(): ?string
    {
        return  __(AdminNavigation::DISCOUNT_RESOURCE['group']);
    }

    public static function getModelLabel(): string
    {
        return __('custom.models.discount');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.discounts');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static ?int $navigationSort = AdminNavigation::DISCOUNT_RESOURCE['sort'];

    protected static ?string $model = Discount::class;

    protected static bool $isGloballySearchable = true;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = AdminNavigation::DISCOUNT_RESOURCE['icon'];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('tabs')->columnSpan(2)->tabs([
                    Tab::make(__('custom.models.discount.tabs.informations'))
                        ->schema([
                            TextInput::make('name')
                                ->unique(ignoreRecord: true)
                                ->label(__('custom.models.discount.name'))
                                ->required()
                                ->columnSpan(2),

                            Textarea::make('description')
                                ->rows(4)
                                ->label(__('custom.models.discount.description'))
                                ->columnSpan(2),
                        ]),
                    Tab::make(__('custom.models.discount.tabs.reduction'))
                        ->schema([
                            // MoneyInput::make('amount')
                            //     ->label(__('custom.models.discount.amount'))
                            //     ->default(0)
                            //     ->required()
                            //     ->locale(__('custom.currency.local.dzd'))
                            //     ->columnSpan(2),

                            TextInput::make('percentage')
                                ->label(__('custom.models.discount.percentage'))
                                ->numeric()
                                ->default(0)
                                ->rules(['min:0', 'max:100'])
                                ->required()
                                ->columnSpan(2),
                        ]),
                    Tab::make(__('custom.models.discount.tabs.period'))
                        ->schema([
                            DatePicker::make('from')
                                ->native(false)
                                ->label(__('custom.models.discount.from'))
                                ->required(),

                            DatePicker::make('to')
                                ->native(false)
                                ->label(__('custom.models.discount.to'))
                                ->required(),
                        ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(true)
            ->columns([
                TextColumn::make('name')
                    ->label(__('custom.models.discount.name'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('subscriptions.name')
                    ->default(__('custom.models.discount.subscriptions.empty'))
                    ->badge()->color('primary')
                    ->sortable()
                    ->searchable()
                    ->label(__('custom.models.discount.subscriptions')),

                // MoneyColumn::make('amount')
                //     ->badge()->color('gray')
                //     ->locale(__('custom.currency.local.dzd'))
                //     ->label(__('custom.models.discount.amount')),

                TextColumn::make('percentage')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn($state) => $state . '%')
                    ->label(__('custom.models.discount.percentage')),

                IconColumn::make('is_active')
                    ->label(__('custom.models.promoCode.is_active'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable()
                    ->toggleable()
                    ->alignCenter(),
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
