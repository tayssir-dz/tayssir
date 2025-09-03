<?php

namespace App\Enums\Purchase;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentType: string implements HasColor, HasIcon, HasLabel
{

    case MANUAL = 'manual';
    case CHARGILY = 'chargily';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MANUAL => __('custom.payment.type.manual') ?: ucfirst($this->value),
            self::CHARGILY => __('custom.payment.type.chargily') ?: ucfirst($this->value),
        };
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            self::MANUAL => __('custom.payment.type.manual.description') ?: ucfirst($this->value),
            self::CHARGILY => __('custom.payment.type.chargily.description') ?: ucfirst($this->value),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::MANUAL => 'warning',
            self::CHARGILY => 'primary',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::MANUAL => 'heroicon-o-document-currency-dollar',
            self::CHARGILY => 'heroicon-o-credit-card',
        };
    }
}
