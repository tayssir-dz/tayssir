<?php

namespace App\Filament\Admin\Resources\ChapterResource\RelationManagers;

use App\Enums\ContentDirection;
use App\Enums\QuestionScope;
use App\Enums\QuestionType;
use App\Models\Question;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use ValentinMorice\FilamentJsonColumn\FilamentJsonColumn;

class QuestionJsonUploadAction
{
    public static function make(): Action
    {
        return Action::make('upload_json')
            ->label(__('custom.models.question.json_upload.button'))
            ->icon('heroicon-o-arrow-up-tray')
            ->color('info')
            ->modalHeading(__('custom.models.question.json_upload.modal_heading'))
            ->modalDescription(__('custom.models.question.json_upload.modal_description'))
            ->modalSubmitActionLabel(__('custom.models.question.json_upload.submit_button'))
            ->form([
                FilamentJsonColumn::make('json_data')
                    ->label(__('custom.models.question.json_upload.json_data_label'))
                    ->required()
                    ->columnSpanFull()
                    ->helperText(__('custom.models.question.json_upload.helper_text'))
                    ->accent('#F037A5')
                    ->viewerHeight(400)
                    ->editorHeight(400)
                    ->default('{}'),
            ])
            ->action(function (array $data, $livewire) {
                try {
                    // Decode JSON
                    $jsonData = $data['json_data'];
                    // $jsonData = json_decode($data['json_data'], true);

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        Notification::make()
                            ->title(__('custom.models.question.json_upload.invalid_json'))
                            ->body(__('custom.models.question.json_upload.invalid_json_message', ['error' => json_last_error_msg()]))
                            ->danger()
                            ->send();

                        return;
                    }

                    // Ensure we have an array of questions
                    if (! is_array($jsonData)) {
                        Notification::make()
                            ->title(__('custom.models.question.json_upload.invalid_format'))
                            ->body(__('custom.models.question.json_upload.invalid_format_message'))
                            ->danger()
                            ->send();

                        return;
                    }

                    // If we have a single question (object with 'question' field), wrap it in an array
                    if (isset($jsonData['question'])) {
                        $jsonData = [$jsonData];
                    }

                    $createdCount = 0;
                    $errorCount = 0;
                    $errors = [];

                    // Get the chapter from the relation manager's ownerRecord
                    $chapter = $livewire->getOwnerRecord();

                    foreach ($jsonData as $index => $questionData) {
                        try {
                            // Ensure questionData is an array
                            if (! is_array($questionData)) {
                                $errorCount++;
                                $errors[] = "Question #{$index}: Question data must be an object/array";

                                continue;
                            }

                            // Check required fields first
                            $requiredFields = ['question', 'question_type', 'scope', 'direction', 'options'];
                            $missingFields = [];

                            foreach ($requiredFields as $field) {
                                if (! isset($questionData[$field])) {
                                    $missingFields[] = $field;
                                }
                            }

                            if (! empty($missingFields)) {
                                $errorCount++;
                                $errors[] = "Question #{$index}: Missing required fields: " . implode(', ', $missingFields);

                                continue;
                            }

                            // Make sure options is an array
                            if (! is_array($questionData['options'])) {
                                $errorCount++;
                                $errors[] = "Question #{$index}: 'options' field must be an object/array";

                                continue;
                            }

                            Log::info("Question #{$index}: Data before validation", ['questionData' => $questionData]);

                            // Validate the question data
                            $validator = self::validateQuestionData($questionData);

                            Log::info("Question #{$index}: Validation result", ['passed' => $validator->passes(), 'errors' => $validator->errors()->all()]);

                            if ($validator->fails()) {
                                $errorCount++;
                                $errors[] = "Question #{$index}: " . implode(', ', $validator->errors()->all());

                                continue;
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

                            // Handle hint array boolean conversion
                            if (isset($validatedData['hint']) && is_array($validatedData['hint'])) {
                                foreach ($validatedData['hint'] as $key => $hintItem) {
                                    if (isset($hintItem['is_latex']) && is_string($hintItem['is_latex'])) {
                                        $validatedData['hint'][$key]['is_latex'] = filter_var($hintItem['is_latex'], FILTER_VALIDATE_BOOLEAN);
                                    }
                                }
                            }

                            // Ensure the options array is correctly formatted
                            switch ($validatedData['question_type']) {
                                case QuestionType::TRUE_OR_FALSE->value:
                                    if (isset($validatedData['options']['correct']) && is_string($validatedData['options']['correct'])) {
                                        $validatedData['options']['correct'] = filter_var($validatedData['options']['correct'], FILTER_VALIDATE_BOOLEAN);
                                    }
                                    break;

                                case QuestionType::MULTIPLE_CHOICES->value:
                                    if (isset($validatedData['options']['choices']) && is_array($validatedData['options']['choices'])) {
                                        foreach ($validatedData['options']['choices'] as $key => $choice) {
                                            if (isset($choice['is_correct']) && is_string($choice['is_correct'])) {
                                                $validatedData['options']['choices'][$key]['is_correct'] = filter_var($choice['is_correct'], FILTER_VALIDATE_BOOLEAN);
                                            }
                                            if (isset($choice['option_is_latex']) && is_string($choice['option_is_latex'])) {
                                                $validatedData['options']['choices'][$key]['option_is_latex'] = filter_var($choice['option_is_latex'], FILTER_VALIDATE_BOOLEAN);
                                            }
                                        }
                                    }
                                    break;

                                case QuestionType::PICK_THE_INTRUDER->value:
                                    if (isset($validatedData['options']['words']) && is_array($validatedData['options']['words'])) {
                                        foreach ($validatedData['options']['words'] as $key => $word) {
                                            if (isset($word['is_intruder']) && is_string($word['is_intruder'])) {
                                                $validatedData['options']['words'][$key]['is_intruder'] = filter_var($word['is_intruder'], FILTER_VALIDATE_BOOLEAN);
                                            }
                                            if (isset($word['word_is_latex']) && is_string($word['word_is_latex'])) {
                                                $validatedData['options']['words'][$key]['word_is_latex'] = filter_var($word['word_is_latex'], FILTER_VALIDATE_BOOLEAN);
                                            }
                                        }
                                    }
                                    break;

                                case QuestionType::MATCH_WITH_ARROWS->value:
                                    if (isset($validatedData['options']['pairs']) && is_array($validatedData['options']['pairs'])) {
                                        foreach ($validatedData['options']['pairs'] as $key => $pair) {
                                            if (isset($pair['first_is_latex']) && is_string($pair['first_is_latex'])) {
                                                $validatedData['options']['pairs'][$key]['first_is_latex'] = filter_var($pair['first_is_latex'], FILTER_VALIDATE_BOOLEAN);
                                            }
                                            if (isset($pair['second_is_latex']) && is_string($pair['second_is_latex'])) {
                                                $validatedData['options']['pairs'][$key]['second_is_latex'] = filter_var($pair['second_is_latex'], FILTER_VALIDATE_BOOLEAN);
                                            }
                                        }
                                    }
                                    break;
                            }

                            // Create the question
                            $question = new Question($validatedData);

                            // Save the question and attach it to the chapter
                            $chapter->questions()->save($question);

                            $createdCount++;
                        } catch (\Throwable $e) {
                            $errorCount++;
                            $errors[] = "Question #{$index}: " . $e->getMessage();
                            Log::error('Error uploading question: ' . $e->getMessage(), [
                                'exception' => $e,
                                'questionData' => $questionData ?? 'No question data',
                            ]);
                        }
                    }

                    // Report results
                    if ($createdCount > 0) {
                        Notification::make()
                            ->title(__('custom.models.question.json_upload.success_title'))
                            ->body(__('custom.models.question.json_upload.success_message', ['count' => $createdCount]))
                            ->success()
                            ->send();
                    }

                    if ($errorCount > 0) {
                        Notification::make()
                            ->title(__('custom.models.question.json_upload.errors_title'))
                            ->body(implode("\n", $errors))
                            ->danger()
                            ->send();
                    }
                } catch (\Throwable $e) {
                    Notification::make()
                        ->title(__('custom.models.question.json_upload.upload_failed'))
                        ->body(__('custom.models.question.json_upload.error_message', ['error' => $e->getMessage()]))
                        ->danger()
                        ->send();

                    Log::error('JSON upload error', [
                        'exception' => $e,
                    ]);
                }
            });
    }

    public static function validateQuestionData(array $data): \Illuminate\Validation\Validator
    {
        // Handle the direction field case - ContentDirection enum uses uppercase
        if (isset($data['direction']) && is_string($data['direction'])) {
            $data['direction'] = strtoupper($data['direction']);
        }

        $rules = [
            'question' => 'required|string',
            'hint' => 'nullable|array',
            'explanation_text' => 'nullable|string',
            'question_type' => 'required|string|in:' . implode(',', array_column(QuestionType::cases(), 'value')),
            'scope' => 'required|string|in:' . implode(',', array_column(QuestionScope::cases(), 'value')),
            'direction' => 'required|string|in:' . implode(',', array_column(ContentDirection::cases(), 'value')),
            'question_is_latex' => 'boolean',
            'explanation_text_is_latex' => 'boolean',
            'options' => 'required|array',
        ];

        // Add hint validation rules only if hint is present
        if (isset($data['hint']) && is_array($data['hint'])) {
            // Check if hint is an array of objects or single object
            if (!empty($data['hint']) && is_array($data['hint'][0])) {
                // Array of hint objects
                $rules['hint.*.value'] = 'required|string';
                $rules['hint.*.is_latex'] = 'required|boolean';
            }
        }

        // Convert string booleans to actual booleans
        foreach (['question_is_latex', 'explanation_text_is_latex'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = filter_var($data[$field], FILTER_VALIDATE_BOOLEAN);
            }
        }

        // Convert hint array string booleans to actual booleans
        if (isset($data['hint']) && is_array($data['hint'])) {
            foreach ($data['hint'] as $key => $hintItem) {
                if (isset($hintItem['is_latex']) && is_string($hintItem['is_latex'])) {
                    $data['hint'][$key]['is_latex'] = filter_var($hintItem['is_latex'], FILTER_VALIDATE_BOOLEAN);
                }
            }
        }

        // Add validation rules based on question type
        switch ($data['question_type'] ?? null) {
            case QuestionType::TRUE_OR_FALSE->value:
                $rules['options.correct'] = 'required|boolean';

                // Convert string boolean to actual boolean
                if (isset($data['options']['correct']) && is_string($data['options']['correct'])) {
                    $data['options']['correct'] = filter_var($data['options']['correct'], FILTER_VALIDATE_BOOLEAN);
                }
                break;

            case QuestionType::MULTIPLE_CHOICES->value:
                $rules['options.choices'] = 'required|array|min:2';
                $rules['options.choices.*.option'] = 'required|string';
                $rules['options.choices.*.is_correct'] = 'required|boolean';
                $rules['options.choices.*.option_is_latex'] = 'boolean';

                // Convert string booleans to actual booleans in choices
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

            case QuestionType::FILL_IN_THE_BLANKS->value:
                $rules['options.paragraph'] = 'required|string';
                $rules['options.blanks'] = 'required|array|min:1';
                $rules['options.blanks.*.correct_word'] = 'required|string';
                $rules['options.blanks.*.position'] = 'required|integer|min:1';
                $rules['options.suggestions'] = 'nullable|array';
                break;

            case QuestionType::PICK_THE_INTRUDER->value:
                $rules['options.words'] = 'required|array|min:3';
                $rules['options.words.*.word'] = 'required|string';
                $rules['options.words.*.is_intruder'] = 'required|boolean';
                $rules['options.words.*.word_is_latex'] = 'boolean';

                // Convert string booleans to actual booleans in words
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
                $rules['options.pairs'] = 'required|array|min:2';
                $rules['options.pairs.*.first'] = 'required|string';
                $rules['options.pairs.*.second'] = 'required|string';
                $rules['options.pairs.*.first_is_latex'] = 'boolean';
                $rules['options.pairs.*.second_is_latex'] = 'boolean';

                // Convert string booleans to actual booleans in pairs
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

        return Validator::make($data, $rules);
    }
}
