<?php

namespace App\Filament\Resources\ChapterResource\RelationManagers\Question_types;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class PickTheIntruder extends QuestionType
{
    public static function getType(): string
    {
        return 'pick_the_intruder';
    }

    public static function getSchema(): Component
    {
        return Repeater::make('options')
            ->schema([
                TextInput::make('word')
                    ->required()
                    ->minLength(1)
                    ->label(__('custom.models.question.word')),
                Toggle::make('is_intruder')
                    ->inline(false)
                    ->label(__('custom.models.question.word.is_intruder')),
            ])
            ->columns(2)
            ->minItems(3)
            ->maxItems(8)
            ->columnSpanFull()
            ->label(__('custom.models.question.words'))
            ->live()
            ->afterStateUpdated(function ($state, callable $set) {
                if (!is_array($state)) return;

                // Remove empty words
                $state = array_filter($state, fn($option) =>
                    isset($option['word']) && !empty($option['word'])
                );

                // Ensure exactly one word is marked as intruder
                $intruderCount = 0;
                foreach ($state as $option) {
                    if (isset($option['is_intruder']) && $option['is_intruder']) {
                        $intruderCount++;
                    }
                }

                if ($intruderCount !== 1 && count($state) > 0) {
                    foreach ($state as &$option) {
                        $option['is_intruder'] = false;
                    }
                    $state[array_key_first($state)]['is_intruder'] = true;
                    $set('options', array_values($state));
                }
            });
    }
}
