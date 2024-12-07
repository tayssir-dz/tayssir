<?php

namespace App\Filament\Resources\ChapterResource\RelationManagers\Question_types;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;

class TrueOrFalse extends QuestionType
{
    public static function getType(): string
    {
        return 'true_or_false';
    }

    public static function getSchema(): Component
    {
        return Field::make('options')
            ->label(__('questions.correct_answer'))
            ->view('components.forms.true-false-toggle')
            ->default(['correct' => false])
            ->afterStateHydrated(function (Field $component, $state) {
                if (!is_array($state) || !isset($state['correct'])) {
                    $component->state(['correct' => false]);
                }
            })
            ->beforeStateDehydrated(function (Field $component, $state) {
                return ['correct' => (bool) ($state['correct'] ?? false)];
            })
            ->required();
    }
}
