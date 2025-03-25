<?php

namespace App\Filament\Resources\ChapterResource\RelationManagers\Question_types;

use App\Enums\QuestionType as QuestionTypeEnum;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

class PickTheIntruder extends QuestionType
{
    public static function getType(): string
    {
        return QuestionTypeEnum::PICK_THE_INTRUDER->value;
    }

    public static function getSchema(): array
    {
        return [
            Repeater::make('words')
                ->schema([
                    Group::make()->schema([
                        Textarea::make('word')
                            ->rows(1)
                            ->required()
                            ->columnSpan(8)
                            ->minLength(1)
                            ->label(trans('custom.models.question.word')),
                        Toggle::make('word_is_latex')
                            ->inline(false)
                            ->label(trans('custom.models.question.is_latex'))
                            ->columnSpan(2)
                            ->default(false),
                    ])->columns(10),
                    Toggle::make('is_intruder')
                        ->inline(false)
                        ->default(false)
                        ->label(trans('custom.models.question.word.is_intruder'))
                        ->reactive()
                        ->fixIndistinctState(),
                ])
                ->columns(1)
                ->minItems(3)
                ->maxItems(8)
                ->defaultItems(3)
                // ->reorderableWithButtons()
                ->collapsible()
                ->collapsed()
                // ->itemLabel(fn(array $state): ?string => $state['word'] ?? null)
                ->itemLabel(fn(array $state): ?string => $state['word'] . " " . ($state['is_intruder'] ? ("(" . trans('custom.models.question.word.is_intruder') . ')') : null))
                ->columnSpanFull()
                ->label("")
                ->addActionLabel(trans('custom.models.question.add_word'))
                ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                    $intruderCount = collect($data)->where('is_intruder', true)->count();
                    if ($intruderCount !== 1) {
                        throw new \Exception('There must be exactly one intruder.');
                    }
                    return $data;
                })
        ];
    }
}
