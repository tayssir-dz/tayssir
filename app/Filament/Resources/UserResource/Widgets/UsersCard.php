<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Filament\Widgets\MoneyStat;
use App\Models\User;
use App\Models\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Spatie\FilamentSimpleStats\SimpleStat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class UsersCard extends BaseWidget
{
    use HasWidgetShield;
    protected function getStats(): array
    {
        return [
            SimpleStat::make(User::class)->last30Days()->dailyCount()
                ->label(__('custom.stats.users.new'))
                ->description(__('custom.stats.users.last30Days'))
                ->descriptionIcon('heroicon-o-users')
                ->color("success"),


            SimpleStat::make(User::class)->lastDays(30)->dailyCount()
                ->label(__('users in '))
                ->description(__('10 days'))
                ->descriptionIcon('heroicon-o-users')
                ->color("success"),

            SimpleStat::make(model: Card::class)
                ->last30Days()
                ->dailySum('price'),

            SimpleStat::make(Card::class)
                ->last30Days()
                ->dailyCount(),

            MoneyStat::make(Card::class)
                ->last30Days()
                ->dailySum('price'),
        ];
    }
}
