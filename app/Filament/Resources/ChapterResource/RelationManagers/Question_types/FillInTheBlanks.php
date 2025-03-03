<?php

namespace App\Filament\Resources\ChapterResource\RelationManagers\Question_types;

use App\Enums\QuestionType as QuestionTypeEnum;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;

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

                    TagsInput::make('suggestions')
                        ->label(trans('custom.models.question.fill_blank.suggestions'))
                        ->helperText(trans('custom.models.question.fill_blank.suggestions_help'))
                        ->placeholder(trans('custom.models.question.fill_blank.suggestions_placeholder')),

                    Repeater::make('blanks')
                        ->schema([
                            TextInput::make('correct_word')
                                ->required()
                                ->label(trans('custom.models.question.fill_blank.correct_word'))
                                ->maxLength(255),
                            TextInput::make('position')
                                ->required()
                                ->label(trans('custom.models.question.fill_blank.position'))
                                ->prefix('[')
                                ->suffix(']')
                                ->maxLength(2)
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(10),
                        ])
                        ->live()
                        ->createItemButtonLabel(trans('custom.models.question.fill_blank.add_blank'))
                        ->defaultItems(2)
                        ->minItems(1)
                        ->maxItems(5)
                        ->columns(2)
                        ->reorderable()
                        ->collapsible()
                        ->collapsed()
                        ->label(trans('custom.models.question.fill_blank.blanks'))
                        ->itemLabel(fn(array $state): ?string => isset ($state['correct_word'], $state['position']) ? '[' . $state['position'] . '] ' . $state['correct_word'] : null)
                        ->columnSpanFull()
                        ->addActionLabel(trans('custom.models.question.fill_blank.add_blank'))
                ])
                ->columnSpanFull()
        ];
    }

    public static function getDefaultOptions(array $data): array
    {
        return [
            'paragraph' => '',
            'blanks' => [],
            'suggestions' => []
        ];
    }

    public static function saveFormState(mixed $record, array &$data): void
    {
        // Any custom logic needed before saving
    }
}
