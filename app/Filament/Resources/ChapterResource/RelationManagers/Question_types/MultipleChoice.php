<?php

namespace App\Filament\Resources\ChapterResource\RelationManagers\Question_types;

use App\Enums\QuestionType as QuestionTypeEnum;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
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
                            TextInput::make('option')
                                ->required()
                                ->label(trans('custom.models.question.option')),
                            Toggle::make('is_correct')
                                ->label(trans('custom.models.question.option.iscorrect'))
                                ->default(false),
                        ])
                        ->defaultItems(4)
                        ->minItems(2)
                        ->maxItems(6)
                        ->columns(2)
                        ->reorderableWithButtons()
                        ->collapsible()
                        ->label("")
                        ->itemLabel(fn(array $state): ?string => $state['option'] ?? null)
                        ->columnSpanFull()
                        ->addActionLabel(trans('custom.models.question.add_option'))
                ])
        ];
    }
}
