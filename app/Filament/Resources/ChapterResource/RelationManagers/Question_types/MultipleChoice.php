<?php

namespace App\Filament\Resources\ChapterResource\RelationManagers\Question_types;

use App\Enums\QuestionType as QuestionTypeEnum;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

class MultipleChoice extends QuestionType
{
    public static function getType(): string
    {
        return QuestionTypeEnum::MULTIPLE_CHOICES->value;
    }

    public static function getSchema(): array
    {
        return [
            Group::make()
                ->schema([
                    Repeater::make('choices')
                        ->schema([
                            Group::make()->schema([
                                Textarea::make('option')
                                    ->rows(1)
                                    ->required()
                                    ->columnSpan(8)
                                    ->label(trans('custom.models.question.option')),
                                Toggle::make('option_is_latex')
                                    ->inline(false)
                                    ->label(trans('custom.models.question.is_latex'))
                                    ->columnSpan(2)
                                    ->default(false),
                            ])->columns(10),
                            Toggle::make('is_correct')
                                ->inline(false)
                                ->label(trans('custom.models.question.option.iscorrect'))
                                ->default(false),
                        ])
                        ->defaultItems(4)
                        ->minItems(2)
                        ->maxItems(6)
                        ->columns(1)
                        // ->reorderableWithButtons()
                        ->collapsible()
                        ->collapsed()
                        ->label("")
                        ->itemLabel(fn(array $state): ?string => $state['option'] . " " . ($state['is_correct'] ? ("(" . trans('custom.models.question.option.iscorrect') . ')') : null))
                        ->columnSpanFull()
                        ->addActionLabel(trans('custom.models.question.add_option'))
                ])
        ];
    }
}
