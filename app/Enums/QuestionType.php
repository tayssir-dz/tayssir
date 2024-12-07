<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum QuestionType: string implements HasLabel, HasColor, HasIcon
{
    case MULTIPLE_CHOICES = 'multiple_choices';
    case FILL_IN_THE_BLANKS = 'fill_in_the_blanks';
    case PICK_THE_INTRUDER = 'pick_the_intruder';
    case TRUE_OR_FALSE = 'true_or_false';
    case MATCH_WITH_ARROWS = 'match_with_arrows';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MULTIPLE_CHOICES => __('custom.models.question.types.multiple_choices'),
            self::FILL_IN_THE_BLANKS => __('custom.models.question.types.fill_in_the_blanks'),
            self::PICK_THE_INTRUDER => __('custom.models.question.types.pick_the_intruder'),
            self::TRUE_OR_FALSE => __('custom.models.question.types.true_or_false'),
            self::MATCH_WITH_ARROWS => __('custom.models.question.types.match_with_arrows'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::MULTIPLE_CHOICES => 'primary',
            self::FILL_IN_THE_BLANKS => 'success',
            self::PICK_THE_INTRUDER => 'danger',
            self::TRUE_OR_FALSE => 'info',
            self::MATCH_WITH_ARROWS => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::MULTIPLE_CHOICES => 'heroicon-o-list-bullet',
            self::FILL_IN_THE_BLANKS => 'heroicon-o-pencil-square',
            self::PICK_THE_INTRUDER => 'heroicon-o-magnifying-glass',
            self::TRUE_OR_FALSE => 'heroicon-o-check-circle',
            self::MATCH_WITH_ARROWS => 'heroicon-o-arrows-right-left',
        };
    }
}

