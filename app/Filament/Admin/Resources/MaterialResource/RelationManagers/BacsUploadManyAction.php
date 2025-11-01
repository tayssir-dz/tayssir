<?php

namespace App\Filament\Admin\Resources\MaterialResource\RelationManagers;

use App\Models\Bac;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Tables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BacsUploadManyAction
{
    const MAX_FILES = 20;

    public static function make()
    {
        return Tables\Actions\Action::make('upload_many')
            ->slideOver()
            ->label(__('custom.actions.bacs.upload_many'))
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
                    ->label(__('custom.forms.bac.upload_many.files_label')),
            ])
            ->action(function ($data, $livewire) {
                $files = $data['files'] ?? [];

                if (empty($files)) {
                    Notification::make()
                        ->title(__('custom.notifications.bacs.upload_warning_title'))
                        ->body(__('custom.notifications.bacs.upload_warning_message'))
                        ->warning()
                        ->send();

                    return;
                }

                $materialId = $livewire->ownerRecord->id;
                $successCount = 0;
                $failedCount = 0;

                foreach ($files as $filePath) {
                    try {
                        // Construct the full path
                        $fullPath = storage_path('app/' . $filePath);

                        // Verify file exists
                        if (!file_exists($fullPath)) {
                            $failedCount++;
                            continue;
                        }

                        // Extract filename without extension
                        $filename = pathinfo($filePath, PATHINFO_FILENAME);

                        // Generate title: replace - and _ with spaces, apply title case
                        $title = Str::title(str_replace(['-', '_'], ' ', $filename));

                        // Create bac record
                        $bac = Bac::create([
                            'title' => $title,
                            'description' => '',
                            'material_id' => $materialId,
                            'is_active' => true,
                        ]);

                        // Attach file to Spatie Media Library
                        $bac->addMedia($fullPath)->toMediaCollection('pdf');

                        // Delete temporary file
                        Storage::disk('local')->delete($filePath);

                        $successCount++;
                    } catch (\Exception $e) {
                        $failedCount++;
                    }
                }

                if ($successCount > 0) {
                    Notification::make()
                        ->title(__('custom.notifications.bacs.upload_success_title'))
                        ->body(__('custom.notifications.bacs.upload_success_message', ['count' => $successCount]))
                        ->success()
                        ->send();
                }

                if ($failedCount > 0) {
                    Notification::make()
                        ->title(__('custom.notifications.bacs.upload_error_title'))
                        ->body(__('custom.notifications.bacs.upload_error_message', ['count' => $failedCount]))
                        ->danger()
                        ->send();
                }

                // Refresh the table
                $livewire->resetTable();
            });
    }
}
