<?php

namespace App\Enums\Purchase;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentStatus: string implements HasColor, HasIcon, HasLabel
{
    case PENDING = 'pending';

    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';

    case SUCCEEDED = 'succeeded';
    case FAILED = 'failed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => __('custom.payment.status.pending') ?: ucfirst($this->value),
            self::ACCEPTED => __('custom.payment.status.accepted') ?: ucfirst($this->value),
            self::REJECTED => __('custom.payment.status.rejected') ?: ucfirst($this->value),
            self::SUCCEEDED => __('custom.payment.status.succeeded') ?: ucfirst($this->value),
            self::FAILED => __('custom.payment.status.failed') ?: ucfirst($this->value),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::ACCEPTED, self::SUCCEEDED => 'success',
            self::REJECTED, self::FAILED => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::PENDING => 'heroicon-o-clock',
            self::ACCEPTED => 'heroicon-o-check-circle',
            self::REJECTED => 'heroicon-o-x-circle',
            self::SUCCEEDED => 'heroicon-o-check',
            self::FAILED => 'heroicon-o-exclamation-triangle',
        };
    }
}
