<?php

namespace App\Filament\Admin\Resources\MaterialResource\RelationManagers;

use App\Models\Summary;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Tables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SummariesUploadManyAction
{

    const MAX_FILES = 20;

    public static function make()

    {

        return Tables\Actions\Action::make('upload_many')
            ->slideOver()
            ->label(__('custom.actions.summaries.upload_many'))
            ->color('info')
            ->icon('heroicon-o-document-plus')
            ->form([
                FileUpload::make('files')
                    ->multiple()
                    ->disk('local')
                    ->preserveFilenames()
                    ->directory('temp')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxFiles(self::MAX_FILES)
                    ->label(__('custom.forms.summary.upload_many.files_label')),
            ])
            ->action(function ($data, $livewire) {
                $materialId = $livewire->ownerRecord->id;
                $uploadedFiles = $data['files'] ?? [];

                if (empty($uploadedFiles)) {
                    Notification::make()
                        ->warning()
                        ->title(__('custom.notifications.summaries.upload_warning_title'))
                        ->body(__('custom.notifications.summaries.upload_warning_message'))
                        ->send();
                    return;
                }

                $successCount = 0;
                $errorCount = 0;

                foreach ($uploadedFiles as $filePath) {
                    try {
                        // Get the full path to the file in storage
                        $fullPath = storage_path('app/' . $filePath);

                        if (!file_exists($fullPath)) {
                            $errorCount++;
                            continue;
                        }

                        // Extract filename without extension
                        $filename = pathinfo($fullPath, PATHINFO_FILENAME);

                        // Generate title: replace - and _ with spaces, then apply title case
                        $title = str_replace(['-', '_'], ' ', $filename);
                        $title = trim(Str::title($title));

                        // Create the summary record
                        $summary = Summary::create([
                            'title' => $title,
                            'description' => '',
                            'material_id' => $materialId,
                            'is_active' => true,
                        ]);

                        // Add file to Spatie Media Library
                        $summary->addMedia($fullPath)
                            ->toMediaCollection('pdf');

                        // Delete the temporary file from storage
                        Storage::disk('local')->delete($filePath);

                        $successCount++;
                    } catch (\Exception $e) {
                        $errorCount++;
                    }
                }

                // Show success notification
                if ($successCount > 0) {
                    Notification::make()
                        ->success()
                        ->title(__('custom.notifications.summaries.upload_success_title'))
                        ->body(__('custom.notifications.summaries.upload_success_message', ['count' => $successCount]))
                        ->send();
                }

                if ($errorCount > 0) {
                    Notification::make()
                        ->warning()
                        ->title(__('custom.notifications.summaries.upload_error_title'))
                        ->body(__('custom.notifications.summaries.upload_error_message', ['count' => $errorCount]))
                        ->send();
                }
            });
    }
}
