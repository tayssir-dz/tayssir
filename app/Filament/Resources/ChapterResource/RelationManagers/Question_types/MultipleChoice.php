<?php

namespace App\Filament\Resources\ChapterResource\RelationManagers\Question_types;

use Filament\Forms\Components\Component;
use Filament\Forms;
use App\Models\Question;

class MultipleChoice extends QuestionType
{
    public static function getType(): string
    {
        return 'multiple_choices';
    }

    public static function getSchema(): Component
    {
        return Forms\Components\Repeater::make('options')
            ->schema([
                Forms\Components\TextInput::make('option')
                    ->required()
                    ->label(__('custom.models.question.option')),
                Forms\Components\Toggle::make('is_correct')
                    ->label(__('custom.models.question.option.iscorrect'))
                    ->default(false)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        if ($state) {
                            $options = $get('../../options');
                            foreach ($options as $key => $option) {
                                $set("../../options.{$key}.is_correct", false);
                            }
                            $set('is_correct', true);
                        }
                    }),
            ])
            ->defaultItems(4)  // Changed from 0 to 4
            ->minItems(2)
            ->maxItems(6)
            ->columns(2)
            ->required()
            ->createItemButtonLabel(__('custom.models.question.add_option'))
            ->label(__('custom.models.question.options'))
            ->columnSpanFull();
    }

    public static function getFormState(Question $question): array
    {
        $options = $question->options;

        if (empty($options)) {
            return [
                'options' => [
                    ['option' => '', 'is_correct' => false],
                    ['option' => '', 'is_correct' => false],
                    ['option' => '', 'is_correct' => false],
                    ['option' => '', 'is_correct' => false],
                ],
            ];
        }

        return [
            'options' => $options,
        ];
    }

    public static function saveFormState(Question $question, array $data): void
    {
        // Ensure at least one option is marked as correct
        $hasCorrect = false;
        foreach ($data['options'] as $option) {
            if ($option['is_correct']) {
                $hasCorrect = true;
                break;
            }
        }

        if (!$hasCorrect && !empty($data['options'])) {
            $data['options'][0]['is_correct'] = true;
        }

        $question->update([
            'options' => array_values($data['options']),
        ]);
    }
}
