<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ContentDirection: string implements HasLabel
{
    case RTL = 'RTL';
    case LTR = 'LTR';
    case INHERIT = 'INHERIT';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::RTL => __('custom.direction.rtl'),
            self::LTR => __('custom.direction.ltr'),
            self::INHERIT => __('custom.direction.inherit'),
        };
    }
}
