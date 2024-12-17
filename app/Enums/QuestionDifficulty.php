<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum QuestionDifficulty: string implements HasLabel, HasColor, HasIcon
{
    case EASY = 'easy';
    case MEDIUM = 'medium';
    case HARD = 'hard';
    public function points(): int
    {
        return match ($this) {
            self::EASY => 1,
            self::MEDIUM => 2,
            self::HARD => 3,
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::EASY => __('custom.models.question.difficulty.easy'),
            self::MEDIUM => __('custom.models.question.difficulty.medium'),
            self::HARD => __('custom.models.question.difficulty.hard'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::EASY => 'success',
            self::MEDIUM => 'warning',
            self::HARD => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::EASY => 'heroicon-o-face-smile',
            self::MEDIUM => 'heroicon-o-face-frown',
            self::HARD => 'heroicon-o-face-frown',
        };
    }
}
