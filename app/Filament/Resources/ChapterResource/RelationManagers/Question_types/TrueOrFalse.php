<?php

namespace App\Filament\Resources\ChapterResource\RelationManagers\Question_types;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;
use App\Enums\QuestionType as QuestionTypeEnum;

class TrueOrFalse extends QuestionType
{
    public static function getType(): string
    {
        return QuestionTypeEnum::TRUE_OR_FALSE->value;
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
                ->default(false)
                ->live()
                ->required()
                ->afterStateHydrated(function (Toggle $component, $state) {
                    $component->state((bool) ($state ?? false));
                })
                ->dehydrateStateUsing(fn($state) => (bool) $state)
                ->rules(['boolean'])
        ];
    }
}
