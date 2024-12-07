<?php

namespace App\Filament\Resources\ChapterResource\RelationManagers\Question_types;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class PickTheIntruder extends QuestionType
{
    public static function getType(): string
    {
        return 'pick_the_intruder';
    }

    public static function getSchema(): array
    {
        return [
            Repeater::make('words')
                ->schema([
                    TextInput::make('word')
                        ->required()
                        ->minLength(1)
                        ->label(trans('custom.models.question.word')),
                    Toggle::make('is_intruder')
                        ->inline()
                        ->default(false)
                        ->label(trans('custom.models.question.word.is_intruder'))
                        ->reactive(),
                ])
                ->columns(2)
                ->minItems(3)
                ->maxItems(8)
                ->defaultItems(3)
                ->columnSpanFull()
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
