<?php

namespace App\Filament\Resources\ChapterResource\RelationManagers\Question_types;

use App\Enums\QuestionType as QuestionTypeEnum;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

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
                    Group::make()->schema([
                        Textarea::make('first')
                            ->rows(1)
                            ->required()
                            ->columnSpan(8)
                            ->minLength(1)
                            ->label(trans('custom.models.question.duo.first')),
                        Toggle::make('first_is_latex')
                            ->inline(false)
                            ->label(trans('custom.models.question.is_latex'))
                            ->columnSpan(2)
                            ->default(false),
                    ])->columns(10),
                    Group::make()->schema([
                        Textarea::make('second')
                            ->rows(1)
                            ->required()
                            ->columnSpan(8)
                            ->minLength(1)
                            ->label(trans('custom.models.question.duo.second')),
                        Toggle::make('second_is_latex')
                            ->inline(false)
                            ->label(trans('custom.models.question.is_latex'))
                            ->columnSpan(2)
                            ->default(false),
                    ])->columns(10),
                ])
                ->columns(1)
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
