<?php

namespace App\Filament\Resources\SubscriptionResource\RelationManagers;

use App\Filament\Resources\SubscriptionResource\RelationManagers\SubscriptionCardLib;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Table;

class SubscriptionCardsRelationManager extends RelationManager
{
    public static function getModelLabel(): string
    {
        return __('custom.models.subscriptionCard');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.subscriptionCards');
    }
    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('custom.models.subscriptionCards');
    }
    protected static string $relationship = 'subscriptionCards';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('code')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color("gray")
                    ->label(__("custom.models.subscriptionCard.code"))
                    ->limit(8)
                    ->size(TextColumnSize::Large),

                Tables\Columns\TextColumn::make('user.email')
                    ->sortable()
                    ->searchable()
                    ->default(__("custom.models.subscriptionCard.user.empty"))
                    ->label(__("custom.models.user")),
                Tables\Columns\TextColumn::make('redeemed_at')
                    ->sortable()
                    ->badge()
                    ->default(__("custom.models.subscriptionCard.redeemed_at.empty"))
                    ->label(__("custom.models.subscriptionCard.redeemed_at"))

            ])
            ->filters(SubscriptionCardLib::getFilters())
            ->headerActions(SubscriptionCardLib::getHeaderActions($this->getOwnerRecord()))
            ->actions(SubscriptionCardLib::getActions())
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
