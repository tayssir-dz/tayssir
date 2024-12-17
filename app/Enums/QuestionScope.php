<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum QuestionScope: string implements HasLabel, HasColor, HasIcon
{
    case EXERCICE = 'exercice';
    case LESSON = 'lesson';

    // public function points(): int
    // {
    //     return match ($this) {
    //         self::EXERCICE => 1,
    //         self::LESSON => 2,
    //     };
    // }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::EXERCICE => __('custom.models.question.scope.exercice'),
            self::LESSON => __('custom.models.question.scope.lesson'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::EXERCICE => 'primary',
            self::LESSON => 'primary',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::EXERCICE => 'heroicon-o-puzzle-piece',
            self::LESSON => 'heroicon-o-book-open',
        };
    }
}
