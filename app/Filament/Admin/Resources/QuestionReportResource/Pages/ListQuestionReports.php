<?php

namespace App\Filament\Admin\Resources\QuestionReportResource\Pages;

use App\Filament\Admin\Resources\QuestionReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuestionReports extends ListRecords
{
    protected static string $resource = QuestionReportResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
