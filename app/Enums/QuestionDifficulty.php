<?php

namespace App\Enums;

enum QuestionDifficulty
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
}
