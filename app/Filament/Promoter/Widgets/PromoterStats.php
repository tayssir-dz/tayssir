<?php

namespace App\Filament\Promoter\Widgets;

use App\Enums\Purchase\PaymentStatus;
use App\Models\Payment;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class PromoterStats extends BaseWidget
{
    protected string|array|int $columnSpan = 3;
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $promoter = Filament::auth()?->user();
        $promoterId = $promoter?->id;

        if (!$promoterId) {
            // Fallback: show zeros when no promoter authenticated
            return [
                Stat::make(__('promoter_space.widgets.stats.total_activations'), 0)
                    ->icon('heroicon-o-bolt')
                    ->color('primary'),
                Stat::make(__('promoter_space.widgets.stats.unique_students'), 0)
                    ->icon('heroicon-o-users')
                    ->color('info'),
                Stat::make(__('promoter_space.widgets.stats.total_discount'), number_format(0, 2))
                    ->icon('heroicon-o-tag')
                    ->color('warning'),
                Stat::make(__('promoter_space.widgets.stats.total_margin'), number_format(0, 2))
                    ->icon('heroicon-o-banknotes')
                    ->color('success'),
            ];
        }

        $statuses = [PaymentStatus::ACCEPTED->value, PaymentStatus::SUCCEEDED->value];

        // Base scoped query: accepted/succeeded payments tied to this promoter's promo codes
        $base = Payment::query()
            ->whereIn('status', $statuses)
            ->whereHas('promoCode', fn($q) => $q->where('promoter_id', $promoterId));

        $totals = [
            'activations' => (clone $base)->count(),
            'unique_students' => (clone $base)->distinct('user_id')->count('user_id'),
            'total_discount' => (float) ((clone $base)->sum('promocode_amount')),
            'total_margin' => (float) ((clone $base)->sum('promoter_margin_amount')),
        ];

        return [
            Stat::make(__('promoter_space.widgets.stats.total_activations'), $totals['activations'])
                ->icon('heroicon-o-bolt')
                ->color('primary'),

            Stat::make(__('promoter_space.widgets.stats.unique_students'), $totals['unique_students'])
                ->icon('heroicon-o-users')
                ->color('info'),

            Stat::make(__('promoter_space.widgets.stats.total_discount'), number_format($totals['total_discount'], 2))
                ->icon('heroicon-o-tag')
                ->color('warning'),

            Stat::make(__('promoter_space.widgets.stats.total_margin'), number_format($totals['total_margin'], 2))
                ->icon('heroicon-o-banknotes')
                ->color('success'),
        ];
    }
}
