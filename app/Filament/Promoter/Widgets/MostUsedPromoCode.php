<?php

namespace App\Filament\Promoter\Widgets;

use App\Enums\Purchase\PaymentStatus;
use App\Models\Payment;
use App\Models\PromoCode;
use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class MostUsedPromoCode extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 3;

    public function getHeading(): string
    {
        return __('promoter_space.widgets.most_used.heading');
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return __('promoter_space.widgets.most_used.empty.title');
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return __('promoter_space.widgets.most_used.empty.description');
    }

    public function table(Table $table): Table
    {
        $promoterId = Filament::auth()?->user()?->id;

        $statuses = [PaymentStatus::ACCEPTED->value, PaymentStatus::SUCCEEDED->value];

        // Compute aggregates per promo code for the current promoter
        $query = PromoCode::query()
            ->where('promoter_id', $promoterId)
            ->leftJoin('payments', function ($join) use ($statuses) {
                $join->on('payments.promo_code_id', '=', 'promo_codes.id')
                    ->whereIn('payments.status', $statuses);
            })
            ->select([
                'promo_codes.id',
                'promo_codes.code',
                DB::raw('COUNT(payments.id) as activations'),
                DB::raw('COALESCE(SUM(payments.promocode_amount), 0) as total_discount'),
                DB::raw('COALESCE(SUM(payments.promoter_margin_amount), 0) as total_margin'),
            ])
            ->groupBy('promo_codes.id', 'promo_codes.code')
            ->havingRaw('COUNT(payments.id) > 0')
            ->orderByDesc('activations')
            ->limit(1);

        return $table
            ->query(fn(): Builder => $query)
            ->columns([
                TextColumn::make('code')
                    ->label(__('promoter_space.widgets.most_used.columns.code'))
                    ->badge()
                    ->color('primary'),
                TextColumn::make('activations')
                    ->label(__('promoter_space.widgets.most_used.columns.activations'))
                    ->formatStateUsing(fn($state) => (int) $state),
                TextColumn::make('total_discount')
                    ->label(__('promoter_space.widgets.most_used.columns.total_discount'))
                    ->formatStateUsing(fn($state) => number_format((float) $state, 2)),
                TextColumn::make('total_margin')
                    ->label(__('promoter_space.widgets.most_used.columns.total_margin'))
                    ->formatStateUsing(fn($state) => number_format((float) $state, 2)),
            ])
            ->paginated(false);
    }
}
