<?php

namespace App\Filament\Resources\ChapterResource\RelationManagers\Question_types;

use App\Enums\QuestionType as QuestionTypeEnum;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class FillInTheBlanks extends QuestionType
{
    public static function getType(): string
    {
        return QuestionTypeEnum::FILL_IN_THE_BLANKS->value;
    }

    public static function getSchema(): array
    {
        return [
            Group::make()
                ->schema([
                    Textarea::make('paragraph')
                        ->live()
                        ->label(trans('custom.models.question.fill_blank.paragraph'))
                        ->required()
                        ->helperText(trans('custom.models.question.fill_blank.paragraph_help')),

                    Repeater::make('answers')
                        ->schema([
                            TextInput::make('word')
                                ->required()
                                ->label(trans('custom.models.question.fill_blank.word'))
                                ->maxLength(255),
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
                        ->live()
                        ->createItemButtonLabel(trans('custom.models.question.fill_blank.add_answer'))
                        ->defaultItems(2)
                        ->minItems(1)
                        ->maxItems(5)
                        ->columns(2)
                        ->reorderableWithButtons()
                        ->collapsible()
                        ->label(trans('custom.models.question.fill_blank.answers'))
                        ->itemLabel(fn(array $state): ?string => $state['word'] ?? null)
                        ->columnSpanFull()
                        ->addActionLabel(trans('custom.models.question.fill_blank.add_answer'))
                ])
                ->columnSpanFull()
        ];
    }
}
