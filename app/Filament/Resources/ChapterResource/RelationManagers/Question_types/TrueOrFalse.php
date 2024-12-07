<?php

namespace App\Filament\Resources\ChapterResource\RelationManagers\Question_types;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;

class TrueOrFalse extends QuestionType
{
    public static function getType(): string
    {
        return 'true_or_false';
    }

    public static function getSchema(): array
    {
        return [
            Toggle::make('correct')
                ->label(trans('custom.models.question.true_false.correct_answer'))
                ->onIcon('heroicon-m-check')
                ->offIcon('heroicon-m-x-mark')
                ->onColor('success')
                ->offColor('danger')
                ->inline()
                ->default(0)  // Setting numeric default to ensure proper casting
                ->rules(['boolean'])  // Enforce boolean type
        ];
    }
}
