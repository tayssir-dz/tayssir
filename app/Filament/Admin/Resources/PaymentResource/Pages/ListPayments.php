<?php

namespace App\Filament\Admin\Resources\PaymentResource\Pages;

use App\Filament\Admin\Resources\PaymentResource;
use App\Enums\Purchase\PaymentStatus;
use App\Enums\Purchase\PaymentType;
use Filament\Resources\Components\Tab;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        $tabs = [];

        $tabs['all'] = Tab::make()->label(__('custom.tabs.all'));

        $tabs['manual'] = Tab::make()
            ->label(__('custom.payment.type.manual'))
            ->icon('heroicon-o-document-currency-dollar')
            ->modifyQueryUsing(fn(Builder $query) => $query->where('payment_type', PaymentType::MANUAL->value));

        $tabs['chargily'] = Tab::make()
            ->label(__('custom.payment.type.chargily'))
            ->icon('heroicon-o-credit-card')
            ->modifyQueryUsing(fn(Builder $query) => $query->where('payment_type', PaymentType::CHARGILY->value));

        $tabs['pending'] = Tab::make()
            ->label(__('custom.payment.status.pending'))
            ->icon('heroicon-o-clock')
            ->modifyQueryUsing(fn(Builder $query) => $query->where('status', PaymentStatus::PENDING->value));

        return $tabs;
    }
}
