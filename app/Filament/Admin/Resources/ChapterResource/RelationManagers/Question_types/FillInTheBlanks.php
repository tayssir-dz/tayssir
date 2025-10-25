<?php

namespace App\Filament\Admin\Resources\ChapterResource\RelationManagers\Question_types;

use App\Enums\QuestionType as QuestionTypeEnum;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

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
                    Group::make()->schema([
                        Textarea::make('paragraph')
                            ->rows(1)
                            ->live()
                            ->required()
                            ->columnSpan(8)
                            ->label(trans('custom.models.question.fill_blank.paragraph'))
                            ->helperText(trans('custom.models.question.fill_blank.paragraph_help')),
                        Toggle::make('paragraph_is_latex')
                            ->inline(false)
                            ->label(trans('custom.models.question.is_latex'))
                            ->columnSpan(2)
                            ->default(false),
                    ])->columns(10),

                    Repeater::make('blanks')
                        ->schema([
                            Group::make()->schema([
                                TextInput::make('correct_word')
                                    ->required()
                                    ->columnSpan(6)
                                    ->label(trans('custom.models.question.fill_blank.correct_word'))
                                    ->maxLength(255),
                                TextInput::make('position')
                                    ->required()
                                    ->columnSpan(2)
                                    ->label(trans('custom.models.question.fill_blank.position'))
                                    ->prefix('[')
                                    ->suffix(']')
                                    ->maxLength(2)
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(10),
                                Toggle::make('correct_word_is_latex')
                                    ->inline(false)
                                    ->label(trans('custom.models.question.is_latex'))
                                    ->columnSpan(2)
                                    ->default(false),
                            ])->columns(10),
                        ])
                        ->live()
                        ->createItemButtonLabel(trans('custom.models.question.fill_blank.add_blank'))
                        ->defaultItems(2)
                        ->minItems(1)
                        ->maxItems(5)
                        ->columns(1)
                        ->reorderable()
                        ->collapsible()
                        ->collapsed()
                        ->label(trans('custom.models.question.fill_blank.blanks'))
                        ->itemLabel(fn(array $state): ?string => isset($state['correct_word'], $state['position']) ? '[' . $state['position'] . '] ' . $state['correct_word'] : null)
                        ->columnSpanFull()
                        ->addActionLabel(trans('custom.models.question.fill_blank.add_blank')),

                    Repeater::make('suggestions')
                        ->schema([
                            Group::make()->schema([
                                Textarea::make('value')
                                    ->rows(1)
                                    ->required()
                                    ->columnSpan(8)
                                    ->label(trans('custom.models.question.fill_blank.word'))
                                    ->maxLength(255),
                                Toggle::make('is_latex')
                                    ->inline(false)
                                    ->label(trans('custom.models.question.is_latex'))
                                    ->columnSpan(2)
                                    ->default(false),
                            ])->columns(10),
                        ])
                        ->defaultItems(0)
                        ->minItems(0)
                        ->label(trans('custom.models.question.fill_blank.suggestions'))
                        ->helperText(trans('custom.models.question.fill_blank.suggestions_help'))
                        ->collapsible()
                        ->collapsed()
                        ->columnSpanFull()
                        ->itemLabel(fn(array $state): ?string => isset($state['value']) ? $state['value'] . ($state['is_latex'] ? ' (LaTeX)' : '') : null)
                        ->addActionLabel(trans('custom.models.question.add_option')),
                ])
                ->columnSpanFull(),
        ];
    }

    public static function getDefaultOptions(array $data): array
    {
        return [
            'paragraph' => '',
            'paragraph_is_latex' => false,
            'blanks' => [],
            'suggestions' => [],
        ];
    }

    public static function saveFormState(mixed $record, array &$data): void
    {
        // Any custom logic needed before saving
    }
}
