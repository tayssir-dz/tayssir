<?php

namespace App\Filament\Resources\CardResource\Pages;

use App\Filament\Resources\CardResource;
use App\Filament\Resources\CardResource\CardActions;
use App\Filament\Resources\CardResource\CardFormUtils;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\ListRecords;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCards extends ListRecords
{
    protected static string $resource = CardResource::class;

    public function getTabs(): array
    {
        return [

            'idle' => Tab::make()->label(__('custom.models.card.status.idle'))
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('status', 'idle');
                }),
            'active' => Tab::make()->label(__('custom.models.card.status.active'))
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('status', 'active');
                }),
            'expired' => Tab::make()->label(__('custom.models.card.status.expired'))
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('status', 'expired');
                }),
            'done' => Tab::make()->label(__('custom.models.card.status.done'))
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('status', 'done');
                }),
            'problem' => Tab::make()->label(__('custom.models.card.status.problem'))
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('status', 'problem');
                }),

            'all' => Tab::make()->label(__('custom.models.user.tabs.all')),

        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Actions\Action::make('create-cards')
                ->label(__('custom.models.card.create_cards'))
                ->visible(auth()->user()->can("create_many_card"))
                ->slideOver()
                ->form([
                    Section::make(__("custom.models.card.tab.pricing"))->schema([
                        TextInput::make("number_of_cards")
                            ->label(__("custom.models.card.number_of_cards"))
                            ->numeric()
                            ->minValue(0)
                            ->required(),

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
                            ->live(),

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

                        MoneyInput::make('display_price')
                            ->locale(__("custom.currency.local.dzd"))
                            ->label(__("custom.models.card.display_price"))
                            ->disabled()
                            ->default(0)
                            ->required(),
                    ]),

                    Section::make(__("custom.models.card.tab.subscription"))
                        ->collapsible()
                        ->schema([
                            Select::make("subscription_type")
                                ->required()
                                ->label(__("custom.models.card.subscription_type"))
                                ->options([
                                    "yearly_subscription" => __("custom.models.card.subscription.yearly"),
                                ]),

                            DatePicker::make("expires_at")
                                ->label(__("custom.models.card.expires_at"))
                                ->required(),
                        ]),
                ])
                ->action(fn(array $data) => CardActions::createManyCards($data)),
        ];
    }
}
