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

    public static function getSchema(): array
    {
        return [
            Repeater::make('pairs')
                ->schema([
                    TextInput::make('first')
                        ->required()
                        ->minLength(1)
                        ->label(trans('custom.models.question.duo.first')),
                    TextInput::make('second')
                        ->required()
                        ->minLength(1)
                        ->label(trans('custom.models.question.duo.second')),
                ])
                ->columns(2)
                ->minItems(2)
                ->maxItems(6)
                ->defaultItems(2)
                ->columnSpanFull()
        ];
    }
}
