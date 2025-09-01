<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\AdminNavigation;
use App\Filament\Admin\Resources\SubscriptionResource\Pages;
use App\Filament\Admin\Resources\SubscriptionResource\RelationManagers\SubscriptionCardsRelationManager;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Tables\Columns\MoneyColumn;

class SubscriptionResource extends Resource
{
    public static function getNavigationGroup(): ?string
    {
        return  __(AdminNavigation::SUBSCRIPTION_RESOURCE['group']);
    }

    public static function getModelLabel(): string
    {
        return __('custom.models.subscription');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.subscriptions');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static ?int $navigationSort = AdminNavigation::SUBSCRIPTION_RESOURCE['sort'];

    protected static ?string $model = Subscription::class;

    protected static bool $isGloballySearchable = true;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = AdminNavigation::SUBSCRIPTION_RESOURCE['icon'];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('name')
                        ->label(__('custom.models.subscription.name'))
                        ->unique(ignoreRecord: true)
                        ->required(),

                    Textarea::make('description')
                        ->label(__('custom.models.subscription.description'))
                        ->rows(7),

                    MoneyInput::make('price')
                        ->label(__('custom.models.subscription.price'))
                        ->default(0)
                        ->locale(__('custom.currency.local.dzd')),
                    DatePicker::make('ending_date')
                        ->native(false)
                        ->label(__('custom.models.subscription.ending_date')),

                    Select::make('discounts')
                        ->label(__('custom.models.subscription.discounts'))
                        ->relationship('discounts', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable(),

                    Forms\Components\ColorPicker::make('gradiant_start')
                        ->label('Gradient Start')
                        ->required(),

                    Forms\Components\ColorPicker::make('gradiant_end')
                        ->label('Gradient End')
                        ->required(),

                    Forms\Components\Toggle::make('bottom_color_at_start')
                        ->label('Bottom Color At Start')
                        ->required(),
                ])->columnSpan(2),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->reorderable("id")
            ->paginated(false)
            ->columns([
                TextColumn::make('name')
                    ->label(__('custom.models.subscription.name'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('discounts.name')
                    ->default(__('custom.models.subscription.discounts.empty'))
                    ->badge()->color('primary')
                    ->sortable()
                    ->searchable()
                    ->label(__('custom.models.subscription.discounts')),

                MoneyColumn::make('price')
                    ->label(__('custom.models.subscription.price'))
                    ->badge()
                    ->sortable()
                    ->color('success')
                    ->locale(__('custom.currency.local.dzd')),

                MoneyColumn::make('price_after_discount')
                    ->badge()
                    ->sortable()
                    ->color('success')
                    ->locale(__('custom.currency.local.dzd')),
                // MoneyColumn::make("discount_amount")->badge()->color("success"),
                // TextColumn::make("discount_percentage")->badge()->color("success"),
                // TextColumn::make("ending_date")
                //     ->default("N/A")
                //     ->label(__("custom.models.subscription.ending_date"))
                //     ->sortable(),

                Tables\Columns\ColorColumn::make('gradiant_start')
                    ->label('Gradient Start'),

                Tables\Columns\ColorColumn::make('gradiant_end')
                    ->label('Gradient End'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            SubscriptionCardsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
