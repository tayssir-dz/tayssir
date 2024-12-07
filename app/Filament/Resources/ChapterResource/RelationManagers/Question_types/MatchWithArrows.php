<?php

namespace App\Filament\Resources\ChapterResource\RelationManagers\Question_types;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;

class MatchWithArrows extends QuestionType
{
    public static function getType(): string
    {
        return 'match_with_arrows';
    }

    public static function getSchema(): Component
    {
        return Repeater::make('options')
            ->schema([
                TextInput::make('first')
                    ->required()
                    ->minLength(1)
                    ->label(__('custom.models.question.match.first')),
                TextInput::make('second')
                    ->required()
                    ->minLength(1)
                    ->label(__('custom.models.question.match.second')),
            ])
            ->columns(2)
            ->minItems(2)
            ->maxItems(6)
            ->columnSpanFull()
            ->label(__('custom.models.question.match.pairs'))
            ->live()
            ->afterStateUpdated(function ($state, callable $set) {
                if (!is_array($state)) return;

                // Remove empty pairs
                $state = array_filter($state, fn($pair) =>
                    isset($pair['first']) && !empty($pair['first']) &&
                    isset($pair['second']) && !empty($pair['second'])
                );

                $set('options', array_values($state));
            });
    }
}
