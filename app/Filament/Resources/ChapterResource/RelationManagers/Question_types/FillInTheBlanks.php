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

    public static function getSchema(): Component
    {
        return Group::make([
            Textarea::make('options.paragraph')
                ->label(__('custom.models.question.fill_blank.paragraph'))
                ->required()
                ->helperText(__('custom.models.question.fill_blank.paragraph_help')),

            Repeater::make('options.answers')
                ->schema([
                    TextInput::make('word')
                        ->required()
                        ->label(__('custom.models.question.fill_blank.word')),
                    TextInput::make('placeholder')
                        ->required()
                        ->label(__('custom.models.question.fill_blank.placeholder'))
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
                ->label(__('custom.models.question.fill_blank.words'))
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {
                    if (!is_array($state)) return;

                    // Remove empty answers
                    $state = array_filter($state, fn($answer) =>
                        isset($answer['word']) && !empty($answer['word']) &&
                        isset($answer['placeholder']) && !empty($answer['placeholder'])
                    );

                    $set('options.answers', array_values($state));
                })
        ])
        ->columnSpanFull();
    }
}
