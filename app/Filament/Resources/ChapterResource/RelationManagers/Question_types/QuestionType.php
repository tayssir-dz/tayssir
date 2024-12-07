<?php

namespace App\Filament\Resources\ChapterResource\RelationManagers\Question_types;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;

abstract class QuestionType
{
    abstract public static function getSchema(): array;
    abstract public static function getType(): string;

    public static function make(): Component
    {
        return Group::make(
            static::getSchema()
        )
            ->statePath('options')
            ->visible(fn($get) => $get('question_type') === static::getType())
            ->afterStateHydrated(function ($component, $state) {
                if (is_string($state)) {
                    $component->state(json_decode($state, true));
                }
            })
            ->dehydrateStateUsing(fn($state) => json_encode($state));
    }
}
