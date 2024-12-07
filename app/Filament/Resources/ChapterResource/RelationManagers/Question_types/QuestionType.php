<?php

namespace App\Filament\Resources\ChapterResource\RelationManagers\Question_types;

use Filament\Forms\Components\Component;

abstract class QuestionType
{
    abstract public static function getSchema(): Component;
    abstract public static function getType(): string;
    
    public static function make(): Component
    {
        return static::getSchema()->visible(fn ($get) => $get('question_type') === static::getType());
    }
}
