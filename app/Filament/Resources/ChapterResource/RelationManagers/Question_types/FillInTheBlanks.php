<?php

namespace App\Filament\Resources\ChapterResource\RelationManagers\Question_types;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class FillInTheBlanks extends QuestionType
{
    public static function getType(): string
    {
        return 'fill_in_the_blanks';
    }

    public static function getSchema(): array
    {
        return [
            Group::make([
                Textarea::make('paragraph')
                    ->label(trans('custom.models.question.fill_blank.paragraph'))
                    ->required()
                    ->helperText(trans('custom.models.question.fill_blank.paragraph_help')),

                Repeater::make('answers')
                    ->schema([
                        TextInput::make('word')
                            ->required()
                            ->label(trans('custom.models.question.fill_blank.word')),
                        TextInput::make('placeholder')
                            ->required()
                            ->label(trans('custom.models.question.fill_blank.placeholder'))
                            ->prefix('[')
                            ->suffix(']')
                            ->maxLength(2)
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10),
                    ])
                    ->defaultItems(1)
                    ->columns(2)
                    ->minItems(1)
                    ->maxItems(5)
                    ->label(trans('custom.models.question.fill_blank.answers'))
            ])
                ->columnSpanFull()
        ];
    }
}
