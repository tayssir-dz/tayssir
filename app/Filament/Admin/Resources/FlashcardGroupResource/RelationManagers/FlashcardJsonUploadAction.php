<?php

namespace App\Filament\Admin\Resources\FlashcardGroupResource\RelationManagers;

use App\Models\Flashcard;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use ValentinMorice\FilamentJsonColumn\FilamentJsonColumn;

class FlashcardJsonUploadAction
{
    public static function make(): Action
    {
        return Action::make('upload_json')
            ->label(__('custom.models.flashcard.json_upload.button'))
            ->icon('heroicon-o-arrow-up-tray')
            ->color('info')
            ->modalHeading(__('custom.models.flashcard.json_upload.modal_heading'))
            ->modalDescription(__('custom.models.flashcard.json_upload.modal_description'))
            ->modalSubmitActionLabel(__('custom.models.flashcard.json_upload.submit_button'))
            ->form([
                FilamentJsonColumn::make('json_data')
                    ->label(__('custom.models.flashcard.json_upload.json_data_label'))
                    ->required()
                    ->columnSpanFull()
                    ->helperText(__('custom.models.flashcard.json_upload.helper_text'))
                    ->accent('#F037A5')
                    ->viewerHeight(400)
                    ->editorHeight(400)
                    ->default('{}'),
            ])
            ->action(function (array $data, $livewire) {
                try {
                    // Decode JSON
                    $jsonData = $data['json_data'];

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        Notification::make()
                            ->title(__('custom.models.flashcard.json_upload.invalid_json'))
                            ->body(__('custom.models.flashcard.json_upload.invalid_json_message', ['error' => json_last_error_msg()]))
                            ->danger()
                            ->send();

                        return;
                    }

                    // Ensure we have an array of flashcards
                    if (! is_array($jsonData)) {
                        Notification::make()
                            ->title(__('custom.models.flashcard.json_upload.invalid_format'))
                            ->body(__('custom.models.flashcard.json_upload.invalid_format_message'))
                            ->danger()
                            ->send();

                        return;
                    }

                    // If we have a single flashcard (object with 'title' field), wrap it in an array
                    if (isset($jsonData['title'])) {
                        $jsonData = [$jsonData];
                    }

                    $createdCount = 0;
                    $errorCount = 0;
                    $errors = [];

                    // Get the flashcard group from the relation manager's ownerRecord
                    $flashcardGroup = $livewire->getOwnerRecord();

                    foreach ($jsonData as $index => $flashcardData) {
                        try {
                            // Ensure flashcardData is an array
                            if (! is_array($flashcardData)) {
                                $errorCount++;
                                $errors[] = "Flashcard #{$index}: Flashcard data must be an object/array";

                                continue;
                            }

                            // Check required fields first
                            $requiredFields = ['title'];
                            $missingFields = [];

                            foreach ($requiredFields as $field) {
                                if (! isset($flashcardData[$field]) || empty(trim($flashcardData[$field]))) {
                                    $missingFields[] = $field;
                                }
                            }

                            if (! empty($missingFields)) {
                                $errorCount++;
                                $errors[] = "Flashcard #{$index}: Missing required fields: " . implode(', ', $missingFields);

                                continue;
                            }

                            // Validate the flashcard data
                            $validator = self::validateFlashcardData($flashcardData);

                            if ($validator->fails()) {
                                $errorCount++;
                                $errors[] = "Flashcard #{$index}: " . implode(', ', $validator->errors()->all());

                                continue;
                            }

                            // Prepare the flashcard data
                            $validatedData = $validator->validated();

                            // Create the flashcard
                            $flashcard = new Flashcard($validatedData);

                            // Save the flashcard and attach it to the flashcard group
                            $flashcardGroup->flashcards()->save($flashcard);

                            $createdCount++;
                        } catch (\Throwable $e) {
                            $errorCount++;
                            $errors[] = "Flashcard #{$index}: " . $e->getMessage();
                            Log::error('Error uploading flashcard: ' . $e->getMessage(), [
                                'exception' => $e,
                                'flashcardData' => $flashcardData ?? 'No flashcard data',
                            ]);
                        }
                    }

                    // Report results
                    if ($createdCount > 0) {
                        Notification::make()
                            ->title(__('custom.models.flashcard.json_upload.success_title'))
                            ->body(__('custom.models.flashcard.json_upload.success_message', ['count' => $createdCount]))
                            ->success()
                            ->send();
                    }

                    if ($errorCount > 0) {
                        Notification::make()
                            ->title(__('custom.models.flashcard.json_upload.errors_title'))
                            ->body(implode("\n", $errors))
                            ->danger()
                            ->send();
                    }
                } catch (\Throwable $e) {
                    Notification::make()
                        ->title(__('custom.models.flashcard.json_upload.upload_failed'))
                        ->body(__('custom.models.flashcard.json_upload.error_message', ['error' => $e->getMessage()]))
                        ->danger()
                        ->send();

                    Log::error('JSON upload error', [
                        'exception' => $e,
                    ]);
                }
            });
    }

    public static function validateFlashcardData(array $data): \Illuminate\Validation\Validator
    {
        $rules = [
            'title' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|max:1000',
        ];

        return Validator::make($data, $rules);
    }
}
