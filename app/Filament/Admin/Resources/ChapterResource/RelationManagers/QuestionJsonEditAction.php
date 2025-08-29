<?php

namespace App\Filament\Admin\Resources\ChapterResource\RelationManagers;

use App\Enums\QuestionType;
use App\Models\Question;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Log;
use ValentinMorice\FilamentJsonColumn\FilamentJsonColumn;

class QuestionJsonEditAction
{
    public static function make(): Action
    {
        return Action::make('edit_json')
            ->label(__('custom.models.question.json_edit.button'))
            ->icon('heroicon-o-code-bracket')
            ->color('primary')
            ->modalHeading(__('custom.models.question.json_edit.modal_heading'))
            ->modalDescription(__('custom.models.question.json_edit.modal_description'))
            ->modalSubmitActionLabel(__('custom.models.question.json_edit.submit_button'))
            ->mountUsing(function (Forms\Form $form, Question $record): void {
                // Prepare the question data for JSON editing
                $questionData = [
                    'id' => $record->id,
                    'question' => $record->question,
                    'question_type' => $record->question_type->value,
                    'scope' => $record->scope->value,
                    'direction' => $record->direction->value,
                    'question_is_latex' => $record->question_is_latex,
                    'options' => $record->options,
                ];

                // Add optional fields if they exist
                if (! empty($record->hint)) {
                    $questionData['hint'] = $record->hint;
                }

                if (! empty($record->explanation_text)) {
                    $questionData['explanation_text'] = $record->explanation_text;
                    $questionData['explanation_text_is_latex'] = $record->explanation_text_is_latex;
                }

                $form->fill([
                    'json_data' => json_encode($questionData, JSON_PRETTY_PRINT),
                ]);
            })
            ->form([
                FilamentJsonColumn::make('json_data')
                    ->label(__('custom.models.question.json_edit.json_data_label'))
                    ->required()
                    ->columnSpanFull()
                    ->helperText(__('custom.models.question.json_edit.helper_text'))
                    ->accent('#F037A5')
                    ->viewerHeight(400)
                    ->editorHeight(400),
            ])
            ->action(function (array $data, Question $record): void {
                try {
                    // Decode JSON
                    $jsonData = $data['json_data'];

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        Notification::make()
                            ->title(__('custom.models.question.json_edit.invalid_json'))
                            ->body(__('custom.models.question.json_edit.invalid_json_message', ['error' => json_last_error_msg()]))
                            ->danger()
                            ->send();

                        return;
                    }

                    // Ensure we have a valid question object
                    if (! is_array($jsonData)) {
                        Notification::make()
                            ->title(__('custom.models.question.json_edit.invalid_format'))
                            ->body(__('custom.models.question.json_edit.invalid_format_message'))
                            ->danger()
                            ->send();

                        return;
                    }

                    // Validate the question data
                    $validator = QuestionJsonUploadAction::validateQuestionData($jsonData);

                    if ($validator->fails()) {
                        Notification::make()
                            ->title(__('custom.models.question.json_edit.invalid_format'))
                            ->body(implode(', ', $validator->errors()->all()))
                            ->danger()
                            ->send();

                        return;
                    }

                    // Prepare the question data
                    $validatedData = $validator->validated();

                    // Handle boolean fields that might be strings
                    if (isset($validatedData['question_is_latex'])) {
                        $validatedData['question_is_latex'] = filter_var($validatedData['question_is_latex'], FILTER_VALIDATE_BOOLEAN);
                    }

                    if (isset($validatedData['explanation_text_is_latex'])) {
                        $validatedData['explanation_text_is_latex'] = filter_var($validatedData['explanation_text_is_latex'], FILTER_VALIDATE_BOOLEAN);
                    }

                    // Process options based on question type
                    $validatedData = self::processOptionsBasedOnType($validatedData);

                    // Update the question
                    $record->fill($validatedData);
                    $record->save();

                    Notification::make()
                        ->title(__('custom.models.question.json_edit.success_title'))
                        ->body(__('custom.models.question.json_edit.success_message'))
                        ->success()
                        ->send();
                } catch (\Throwable $e) {
                    Notification::make()
                        ->title(__('custom.models.question.json_edit.update_failed'))
                        ->body(__('custom.models.question.json_edit.error_message', ['error' => $e->getMessage()]))
                        ->danger()
                        ->send();

                    Log::error('JSON edit error', [
                        'exception' => $e,
                        'questionId' => $record->id,
                    ]);
                }
            });
    }

    protected static function processOptionsBasedOnType(array $data): array
    {
        switch ($data['question_type']) {
            case QuestionType::TRUE_OR_FALSE->value:
                if (isset($data['options']['correct']) && is_string($data['options']['correct'])) {
                    $data['options']['correct'] = filter_var($data['options']['correct'], FILTER_VALIDATE_BOOLEAN);
                }
                break;

            case QuestionType::MULTIPLE_CHOICES->value:
                if (isset($data['options']['choices']) && is_array($data['options']['choices'])) {
                    foreach ($data['options']['choices'] as $key => $choice) {
                        if (isset($choice['is_correct']) && is_string($choice['is_correct'])) {
                            $data['options']['choices'][$key]['is_correct'] = filter_var($choice['is_correct'], FILTER_VALIDATE_BOOLEAN);
                        }
                        if (isset($choice['option_is_latex']) && is_string($choice['option_is_latex'])) {
                            $data['options']['choices'][$key]['option_is_latex'] = filter_var($choice['option_is_latex'], FILTER_VALIDATE_BOOLEAN);
                        }
                    }
                }
                break;

            case QuestionType::PICK_THE_INTRUDER->value:
                if (isset($data['options']['words']) && is_array($data['options']['words'])) {
                    foreach ($data['options']['words'] as $key => $word) {
                        if (isset($word['is_intruder']) && is_string($word['is_intruder'])) {
                            $data['options']['words'][$key]['is_intruder'] = filter_var($word['is_intruder'], FILTER_VALIDATE_BOOLEAN);
                        }
                        if (isset($word['word_is_latex']) && is_string($word['word_is_latex'])) {
                            $data['options']['words'][$key]['word_is_latex'] = filter_var($word['word_is_latex'], FILTER_VALIDATE_BOOLEAN);
                        }
                    }
                }
                break;

            case QuestionType::MATCH_WITH_ARROWS->value:
                if (isset($data['options']['pairs']) && is_array($data['options']['pairs'])) {
                    foreach ($data['options']['pairs'] as $key => $pair) {
                        if (isset($pair['first_is_latex']) && is_string($pair['first_is_latex'])) {
                            $data['options']['pairs'][$key]['first_is_latex'] = filter_var($pair['first_is_latex'], FILTER_VALIDATE_BOOLEAN);
                        }
                        if (isset($pair['second_is_latex']) && is_string($pair['second_is_latex'])) {
                            $data['options']['pairs'][$key]['second_is_latex'] = filter_var($pair['second_is_latex'], FILTER_VALIDATE_BOOLEAN);
                        }
                    }
                }
                break;
        }

        return $data;
    }
}
