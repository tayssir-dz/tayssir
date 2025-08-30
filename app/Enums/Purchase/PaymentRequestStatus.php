<?php

namespace App\Enums\Purchase;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentRequestStatus: string implements HasColor, HasIcon, HasLabel
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => __('custom.manual_payment.status.pending', [], app()->getLocale()) ?? ucfirst($this->value),
            self::ACCEPTED => __('custom.manual_payment.status.accepted', [], app()->getLocale()) ?? ucfirst($this->value),
            self::REJECTED => __('custom.manual_payment.status.rejected', [], app()->getLocale()) ?? ucfirst($this->value),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::ACCEPTED => 'success',
            self::REJECTED => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::PENDING => 'heroicon-o-clock',
            self::ACCEPTED => 'heroicon-o-check-circle',
            self::REJECTED => 'heroicon-o-x-circle',
        };
    }
}
