<?php

namespace App\Filament\Resources\ChapterResource\RelationManagers\Question_types;

use App\Enums\QuestionType as QuestionTypeEnum;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;

class MatchWithArrows extends QuestionType
{
    public static function getType(): string
    {
        return QuestionTypeEnum::MATCH_WITH_ARROWS->value;
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
                // ->reorderableWithButtons()
                ->collapsible()
                ->collapsed()
                ->label("")
                ->itemLabel(fn(array $state): ?string => '(' . ($state['first'] ?? '-') . ') → (' . ($state['second'] ?? '-') . ")")
                ->columnSpanFull()
                ->addActionLabel(trans('custom.models.question.add_duo'))
        ];
    }
}
