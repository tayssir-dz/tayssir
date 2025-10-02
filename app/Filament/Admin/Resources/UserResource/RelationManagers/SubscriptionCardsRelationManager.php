<?php

namespace App\Filament\Admin\Resources\UserResource\RelationManagers;

use App\Models\Subscription;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubscriptionCardsRelationManager extends RelationManager
{
    protected static string $relationship = 'subscriptionCards';

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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('code')
            ->modifyQueryUsing(fn($query) => $query->whereNotNull('redeemed_at'))
            ->columns([
                TextColumn::make('subscription.name')
                    ->label(__('custom.models.subscription'))
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                TextColumn::make('redeemed_at')
                    ->label(__('custom.models.subscriptionCard.redeemed_at'))
                    ->since()
                    ->sortable(),
            ])
            ->headerActions([])
            ->actions([
                DeleteAction::make('remove')
                    ->label(__('custom.models.user.actions.remove_subscription'))
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->modalHeading(__('custom.models.user.actions.remove_subscription'))
                    ->modalDescription(__('custom.models.user.actions.remove_subscription.confirm_description'))
                    ->visible(fn($record) => (int) $record->subscription_id !== Subscription::GUEST_ID)
                    ->successNotificationTitle(__('custom.models.user.notices.subscription_removed')),
            ])
            ->bulkActions([]);
    }
}
