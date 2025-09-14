<?php

namespace App\Filament\Admin\Resources\QuestionReportResource\Pages;

use App\Filament\Admin\Resources\QuestionReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuestionReport extends EditRecord
{
    protected static string $resource = QuestionReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
